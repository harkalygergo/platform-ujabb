<?php

namespace App\Controller\Platform\Module\Printbox;

use App\Controller\Platform\Module\Shopify\ShopifyController;
use App\Entity\Platform\Module\Shopify\ECard;

class PrintboxController
{
    private ?int $printboxUserId;

    public function __construct()
    {
        $this->printboxUserId = (int)$_ENV['MODULE_SHOPIFY_API_KEY'];
    }

    public function createECardOrder(ECard $ECard)
    {
        $order = json_decode($ECard->getOrderJSON(), true);
        $projects = json_decode($ECard->getProjects(), true);

        foreach($projects as $project) {
            $this->doPrintboxAction($order['id'], $order['name'], $project, 'addUser');
            $this->doPrintboxAction($order['id'], $order['name'], $project, 'validate');
        }

        $payloadProjects = [];
        foreach ($order['line_items'] as $orderLineItem) {
            if (isset($orderLineItem['properties'])) {
                foreach ($orderLineItem['properties'] as $property) {
                    if ($property['name']==='Project') {
                        $payloadProjects[] = [
                            'uuid'                          => $orderLineItem['properties']['0']['value'],
                            'quantity'                      => ($orderLineItem['fulfillable_quantity'] != 0) ? $orderLineItem['fulfillable_quantity'] : 1,
                            'item_price_net'                => $orderLineItem['price'],
                            'item_price_net_incl_discount'  => $orderLineItem['price'],
                        ];
                    }
                }
            }
        }

        // create order
        $this->doPrintboxOrder($order['name'].'-ecard', $payloadProjects);
        // set order paid to start PDF generation
        $this->doPrintboxAction($order['id'], $order['name'].'-ecard', '', 'setOrderPaid');
    }

    public function doPrintboxOrder($orderName, $payloadProjects)
    {
        $method = 'POST';
        $urlPart = 'orders/';
        $projectHash = '';
        $urlEnd = '';
        $payload = [
            "number"        => $orderName,
            "customer_id"   => $this->printboxUserId,
            'projects'      => $payloadProjects
            //'status'      => 'Paid',
        ];

        return $this->getResult($urlPart, $projectHash, $urlEnd, $method, $payload);
    }

    public function doPrintboxAction($orderId, $orderName, $projectHash, $action)
    {
        switch($action) {
            case 'addUser':
            {
                $method = 'PATCH';
                $urlPart = 'projects/';
                $urlEnd = '/';
                $payload = ['customer_id' => $this->printboxUserId];
                break;
            }
            case 'validate': {
                $method = 'GET';
                $urlPart = 'projects/';
                $urlEnd = '/validate/';
                $payload = [];
                break;
            }
            case 'setOrderPaid': {
                $method = 'PATCH';
                $urlPart = 'orders/';
                $urlEnd = $orderName . '/';
                $payload = [
                    'status' => 'Paid',
                ];
                $projectHash = '';
                break;
            }
        }

        return $this->getResult($urlPart, $projectHash, $urlEnd, $method, $payload);

    }

    public function getResult($urlPart, $projectHash, $urlEnd, $method, $payload)
    {
        $url = $_ENV['MODULE_PRINTBOX_URL'].'/api/ec/v4/' . $urlPart . $projectHash . $urlEnd;
        $result = $this->sendCurl($url, [], $this->getRequestHeader(), $method, $payload);

        return $result;
    }

    private function getPrintBoxToken(): array
    {
        $opt = [
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => [
                'grant_type'    => 'client_credentials',
                'client_id'     => $_ENV['MODULE_PRINTBOX_CLIENT_ID'],
                'client_secret' => $_ENV['MODULE_PRINTBOX_CLIENT_SECRET'],
            ]
        ];
        $result = $this->sendCurl( $_ENV['MODULE_PRINTBOX_URL'].'/o/token/', $opt, [], 0, '');

        if (!$result) {
            return [];
        }

        return json_decode($result, true);
    }

    public function getRequestHeader(): array
    {
        $result = $this->getPrintBoxToken();

        $header = [];
        //$header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $result['access_token'];

        return $header;
    }

    public function sendCurl($url = '', $opt = [], $header = [], string|int $post = 0, array|string $payload = []): bool|string
    {
        $ch = curl_init();

        if (gettype($post) === 'string') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $post);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (!empty($payload) || $payload !== '') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        } else {
            curl_setopt_array($ch, $opt);
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }










    public function goThroughPrintboxProcess(ShopifyController $shopifyController, int|string $orderId)
    {
        /*
         * 1. projektek Printbox felhasználóhoz rendelése
         * 2. Printbox rendelhetősége ellenőrzése
         * 3. Printbox rendelés készítése
         * 4. Printbox rendelés fizetettnek jelölése
         * 5. Printbox render_status ellenőrzése
         * 6. PDF letöltése
         * */
        // use Shopify getOrderById method
        // use ShopifyController getOrderById method


        $order = $shopifyController->getOrderById((int)$orderId);

        // Printbox rendelés készítés

        // add projects to user
        foreach ($order['line_items'] as $orderLineItem) {
            if (isset($orderLineItem['properties'])) {
                foreach ($orderLineItem['properties'] as $property) {
                    if ($property['name']==='Project') {
                        $this->doPrintboxActions($orderId, $order['name'], $property['value'], 'update');
                    }
                }
            }
        }
        // is orderable?
        foreach ($order['line_items'] as $orderLineItem) {
            if (isset($orderLineItem['properties'])) {
                foreach ($orderLineItem['properties'] as $property) {
                    if ($property['name']==='Project') {
                        $validation = json_decode($this->doPrintboxActions($orderId, $order['name'], $property['value'], 'validate'), true);
                        if (!$validation['is_orderable']) {
                            // throw error, project not orderable
                            echo '<h1>HIBA: '. $property['value']. ' projekt nem rendelhető</h1>';
                        }
                    }
                }
            }
        }
        // create order
        $this->doPrintboxActions($orderId, $order['name'], '', 'order');
        // set order to paid
        $this->doPrintboxActions($orderId, $order['name'], '', 'paid');

        header('Location:/app.php');
    }

    public function doPrintboxActions(string|int $orderId, string $orderName, string $projectHash, string $action, $order = null, array $attributes = []): bool|string
    {
        switch($action) {
            case 'view': {
                $method = 'GET';
                $urlPart = 'projects/';
                $urlEnd = '/';
                $payload = [];

                break;
            }
            // set Printbox user
            case 'update':
            {
                $method = 'PATCH';
                $urlPart = 'projects/';
                $urlEnd = '/';
                $payload = ['customer_id' => self::PRINTBOX_USER_ID];
                break;
            }
            case 'validate': {
                $method = 'GET';
                $urlPart = 'projects/';
                $urlEnd = '/validate/';
                $payload = [];
                break;
            }
            case 'order':
            {
                $order = $this->getOrderById((int)$orderId);

                $orderLineItems = $order['line_items'];
                $payloadProjects = [];
                foreach ($orderLineItems as $orderLineItem) {

                    if (isset($orderLineItem['properties'])) {
                        foreach ($orderLineItem['properties'] as $property) {
                            if ($property['name']==='Project') {
                                $payloadProjects[] = [
                                    'uuid'                          => $property['value'],
                                    'quantity'                      => ($orderLineItem['fulfillable_quantity'] != 0) ? $orderLineItem['fulfillable_quantity'] : 1,
                                    'item_price_net'                => $orderLineItem['price'],
                                    'item_price_net_incl_discount'  => $orderLineItem['price'],
                                ];
                            }
                        }
                    }
                }
                $method = 'POST';
                $urlPart = 'orders/';
                $urlEnd = '';
                $payload = [
                    "number"        => $orderName,
                    "customer_id"   => self::PRINTBOX_USER_ID,
                    'projects'      => $payloadProjects
                    //'status'      => 'Paid',
                ];
                $projectHash = '';
                break;
            }
            case 'paid': {
                $method = 'PATCH';
                $urlPart = 'orders/';
                $urlEnd = $orderName . '/';
                $payload = [
                    'status' => 'Paid',
                ];
                $projectHash = '';
                break;
            }
        }

        $url = 'https://paperstories-eu-pbx2.getprintbox.com/api/ec/v4/' . $urlPart . $projectHash . $urlEnd;
        $result = $this->sendCurl($url, [], $this->getRequestHeader(), $method, $payload);

        return $result;

        /*

        // bulk
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'bulk' && isset($_POST['bulk'])) {
            $bulkProjectHashes = $_POST['projectHashes'];
            if ($bulkProjectHashes !== '') {
                $bulkOrderIds = [$orderId];
                $orderId = $_POST['orderID'];
                $action = $_POST['actionButton'];
            }
            $bulkProjectHashes = explode("\n", $bulkProjectHashes);
            $bulkProjectHashes = array_map('trim', $bulkProjectHashes);
            // createbulkorder
            // orderupdate
            // downloadMultiplePDFs
        } else {
            if (is_null($order)) {
                if (get_post_status($orderId)) {
                    $order = new \WC_Order($orderId);
                    $bulkOrderIds = [$orderId];
                }
            } else {
                $bulkOrderIds = [$orderId];
            }
        }

        switch ($action) {
            case 'to-delete':
            {
                $method = 'GET';
                $urlPart = 'projects/';
                $projectHash = '';
                $urlEnd = 'to-delete/?date_from=2022-01-01&date_to=2023-02-28';
                $payload = [];
                break;
            }
            case 'duplicate':
            {
                $method = 'POST';
                $urlPart = 'projects/';
                $urlEnd = '/duplicate/';
                $payload = [
                    'store_id' => 1
                ];
                break;
            }
            case 'userauth':
            {
                $method = 'POST';
                $urlPart = 'pbx-user/';
                $projectHash = '';
                $urlEnd = 'uat/';
                $payload = ["email" => "gergo.harkaly@paperstories.hu"];
                break;
            }
            case 'customers':
            {
                $method = 'GET';
                $urlPart = 'customers/';
                $projectHash = '';
                $urlEnd = '76093/';
                $payload = [];
                break;
            }
            case 'updateName':
            {
                $info = explode('_', $projectHash);
                $projectHash = $info['0'];
                $projectName = $info['1'];
                $method = 'PATCH';
                $urlPart = 'projects/';
                $urlEnd = '/';
                $payload = ['name' => $projectName];
                break;
            }
            case 'preflightRender':
            {
                $info = explode('_', $projectHash);
                $projectHash = $info['0'];
                $projectName = $info['1'];
                $method = 'POST';
                $urlPart = 'projects/';
                $urlEnd = '/render/';
                $payload = ["preflight" => true];
                break;
            }
            case 'downloadPreflight':
            {
                $wp_root_path = str_replace('/wp-content/themes', '', get_theme_root());
                $targetPDF = $wp_root_path . '/printbox/user-preflight/';

                if (file_exists($targetPDF . basename($projectHash) . '_pages.pdf')) {
                    return $targetPDF . basename($projectHash) . '_pages.pdf';
                } else {
                    //$url = 'https://pbx2-paperstories-eu.s3.eu-central-1.amazonaws.com/renders/preflight/' . $projectHash . '.tar';
                    $url = 'https://storage.googleapis.com/pbx2-paperstories-eu/renders/' . $projectHash . '.tar';
                    $tar = file_get_contents($url);
                    $savedFilePath = $wp_root_path . '/printbox/user-preflight/' . $projectHash . '.tar';
                    file_put_contents($savedFilePath, $tar);
                    // unarchive from the tar
                    $phar = new \PharData($savedFilePath);
                    $phar->extractTo($wp_root_path . '/printbox/user-preflight/');
                    //chmod($savedFilePath, 0777);
                    //exec('tar -xf '.$savedFilePath);
                    return $url;
                    break;
                }
            }
            case 'session':
            {
                $method = 'POST';
                $urlPart = 'sessions/';
                $urlEnd = '';
                $projectHash = '';
                $payload = ['customer_id' => $this->getCurrentUserBasedPrintboxUserId()];
                break;
            }
            case 'validate':
            {
                $method = 'GET';
                $urlPart = 'projects/';
                $urlEnd = '/validate/';
                $payload = [];
                break;
            }
            case 'view':
            {
                $method = 'GET';
                $urlPart = 'projects/';
                $urlEnd = '/';
                $payload = [];
                break;
            }
            case 'update':
            {
                $method = 'PATCH';
                $urlPart = 'projects/';
                $urlEnd = '/';
                $payload = ['customer_id' => $this->getCurrentUserBasedPrintboxUserId()];
                break;
            }
            case 'render':
            {
                $method = 'POST';
                $urlPart = 'projects/';
                $urlEnd = '/render/';
                $payload = ["preflight" => false];
                break;
            }
            case 'order':
            {
                $orderItems = $order->get_items();
                $payloadProjects = [];
                foreach ($orderItems as $orderItem) {
                    $projectHash = $orderItem->get_meta($this->itemKey);
                    if ($projectHash !== '') {

                        $payloadProjects[] = [
                            'uuid' => $projectHash,
                            'quantity' => is_null($orderItem) ? 1 : $orderItem->get_quantity(),
                            'item_price_net' => (int)(is_null($orderItem) ? 1 : $orderItem->get_subtotal() / $orderItem->get_quantity()),
                            'item_price_net_incl_discount' => (int)(is_null($order) ? 1 : $orderItem->get_subtotal() / $orderItem->get_quantity()),
                            //'item_price_gross' => (int) (is_null($orderItem) ? 1 : ($orderItem->get_subtotal() + $orderItem->get_subtotal_tax())/$orderItem->get_quantity()),
                            //'item_price_gross_incl_discount' => (int) (is_null($order) ? 1 : ($orderItem->get_subtotal() + $orderItem->get_subtotal_tax())/$orderItem->get_quantity()),
                        ];

                    }
                }

                $method = 'POST';
                $urlPart = 'orders/';
                $urlEnd = '';
                $payload = [
                    "number" => $orderId,
                    "customer_id" => $this->getCurrentUserBasedPrintboxUserId($order),
                    'projects' => $payloadProjects
                    //'status' => 'Paid',
                ];
                $projectHash = '';
                break;
            }

            case 'updatevisszamenolegazelrontottakat':
            {
                $initial_date = '2023-06-01';
                $final_date = '2023-06-30';
                $orders = wc_get_orders([
                        'limit' => -1,
                        'type' => 'shop_order',
                        'date_created' => $initial_date . '...' . $final_date
                    ]
                );

                $results = [];

                foreach ($orders as $order) {
                    $orderItems = $order->get_items();
                    $payloadProjects = [];
                    foreach ($orderItems as $orderItem) {
                        $projectHash = $orderItem->get_meta($this->itemKey);
                        if ($projectHash !== '') {

                            $payloadProjects[] = [
                                'uuid' => $projectHash,
                                'quantity' => 1, //is_null($orderItem) ? 1 : $orderItem->get_quantity(),
                                'item_price_net' => (int)(is_null($orderItem) ? 1 : $orderItem->get_subtotal() / $orderItem->get_quantity()),
                                'item_price_net_incl_discount' => (int)(is_null($order) ? 1 : $orderItem->get_subtotal() / $orderItem->get_quantity()),
                                'item_price_gross' => (int)(is_null($orderItem) ? 1 : ($orderItem->get_subtotal() + $orderItem->get_subtotal_tax()) / $orderItem->get_quantity()),
                                'item_price_gross_incl_discount' => (int)(is_null($order) ? 1 : ($orderItem->get_subtotal() + $orderItem->get_subtotal_tax()) / $orderItem->get_quantity()),
                            ];
                        }
                    }

                    $method = 'PATCH';
                    $urlPart = 'orders/';
                    $projectHash = '';
                    $urlEnd = $order->get_id() . '/';
                    $payload = [
                        'projects' => $payloadProjects
                        //'status' => 'Paid',
                    ];

                    $url = 'https://paperstories-eu-pbx2.getprintbox.com/api/ec/v4/' . $urlPart . $projectHash . $urlEnd;
                    $result = $this->sendCurl($url, [], $this->getRequestHeader(), $method, $payload);
                    $results[$order->get_id()] = $result;

                }
                print_r($results);
                exit;
                break;
            }

            case 'createbulkorder':
            {
                foreach ($bulkOrderIds as $bulkOrderId) {
                    $orderId = $_POST['orderID'];
                    $payloadProjects = [];
                    foreach ($bulkProjectHashes as $projectHash) {
                        $payloadProjects[] = [
                            'uuid' => $projectHash,
                            'quantity' => 1,
                            'item_price_net' => 1,
                            'item_price_net_incl_discount' => 1,
                            'item_price_gross' => 1,
                            'item_price_gross_incl_discount' => 1,
                        ];
                    }

                    $method = 'POST';
                    $urlPart = 'orders/';
                    $urlEnd = '';
                    $payload = [
                        "number" => $orderId,
                        "customer_id" => $this->getCurrentUserBasedPrintboxUserId($order),
                        'projects' => $payloadProjects
                        //'status' => 'Paid',
                    ];
                    $projectHash = '';
                }
                break;
            }
            case 'orderupdate':
            {
                $method = 'PATCH';
                $urlPart = 'orders/';
                $urlEnd = $orderId . '/';
                $urlEnd = $bulkOrderIds['0'] . '/';
                $payload = [
                    'status' => 'Paid',
                ];
                $projectHash = '';
                break;
            }
            case 'viewOrder':
            {
                $method = 'GET';
                $urlPart = 'orders/';
                $urlEnd = $orderId . '/';
                $payload = [];
                $projectHash = '';
                break;
            }
            case 'attributes':
            {
                $method = 'GET';
                $urlPart = 'attributes/';
                $projectHash = '';
                $urlEnd = $attributes['attribute_id'] . '/';
                $payload = [];
                break;
            }
            case 'attribute-values':
            {
                $method = 'GET';
                $urlPart = 'attribute-values/';
                $projectHash = '';
                $urlEnd = $attributes['attribute_values_id'] . '/';
                $payload = [];
                break;
            }
            case 'downloadMultiplePDFs':
            {
                foreach ($bulkOrderIds as $bulkOrderId) {
                    $orderId = $bulkOrderId;
                    $method = 'GET';
                    $urlPart = 'orders/';
                    $urlEnd = $_POST['orderID'] . '/';
                    $payload = [];
                    $projectHash = '';
                    $url = 'https://paperstories-eu-pbx2.getprintbox.com/api/ec/v4/' . $urlPart . $projectHash . $urlEnd;
                    $result = $this->sendCurl($url, [], $this->getRequestHeader(), $method, $payload);
                    $orderArray = json_decode($result, true);
                    $projects = $orderArray['projects'];

                    $attributesArray = [];

                    $wp_root_path = str_replace('/wp-content/themes', '', get_theme_root());
                    //mkdir($wp_root_path.'/printbox/'.$series);
                    foreach ($projects as $project) {
                        $projectHash = $project['uuid'];
                        $projectArray = json_decode($this->doActions($order, $projectHash, 'view'), true);
                        $projectTarURL = 'https://storage.googleapis.com/pbx2-paperstories-eu/renders/' . $projectHash . '.tar';
                        $tar = file_get_contents($projectTarURL);

                        $displayName = [];
                        foreach ($projectArray['params'] as $param) {
                            foreach ($param['attribute_values_ids'] as $attribute) {
                                $attributeValues = json_decode($this->doActions($orderId, $projectHash, 'attribute-values', null, $order, ['attribute_values_id' => $attribute]));

                                if (!array_key_exists($attributeValues->attribute_id, $attributesArray)) {
                                    $attributesArray[$attributeValues->attribute_id] = (json_decode($this->doActions($orderId, $projectHash, 'attributes', null, $order, ['attribute_id' => $attributeValues->attribute_id])))->name;
                                }

                                $displayName[$attributesArray[$attributeValues->attribute_id]] = $attributeValues->display_name->en;
                            }
                        }

                        if (!file_exists($wp_root_path . '/printbox/' . $_POST['orderID'])) {
                            mkdir($wp_root_path . '/printbox/' . $_POST['orderID']);
                        }

                        $targetFileName = $displayName['size'] . '-' . $displayName['theme'] . '.tar';

                        //$targetFileName = $series.'___'.$projectArray['name'].'___'.$projectHash.'.tar';
                        //$targetFileName = $projectArray['name'].'-'.$series.'.tar';
                        $savedFilePath = $wp_root_path . '/printbox/' . $_POST['orderID'] . '/' . $targetFileName;
                        file_put_contents($savedFilePath, $tar);
                        //chmod($savedFilePath, 0777);
                        //exec('tar -xf '.$savedFilePath);
                        echo $targetFileName . "\n";
                    }
                }
                exit;
            }

            case 'createUser':
            {
                $method = 'POST';
                $urlPart = 'customers/';
                $urlEnd = '';
                $projectHash = '';
                $payload = [
                    'username' => 'Toth Betty',
                    'email' => 'toth.bernadett@paperstories.hu',
                ];

                break;
            }

        }

        $url = 'https://paperstories-eu-pbx2.getprintbox.com/api/ec/v4/' . $urlPart . $projectHash . $urlEnd;
        $result = $this->sendCurl($url, [], $this->getRequestHeader(), $method, $payload);

        if ($action === 'session') {
            $this->updateOption($result);
        }

        return $result;
        */
    }

}
