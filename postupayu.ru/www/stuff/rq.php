<?php

function ar2billing($ar) {
    $out = array();
    $j = 0;
    if (count($ar) > 0) {
        try {
//            $client = new SoapClient('http://192.168.2.156:7005/ExchangePayments/ExchangePayments?WSDL', array("trace" => 1, "exceptions" => 0));
            $client = new SoapClient('http://192.168.2.148:7010/ExchangePayments/ExchangePayments?WSDL', array("trace" => 1, "exceptions" => 0, 'login' => "test_ws", 'password' => "test_ws"));
            for ($i = 0; $i < count($ar); $i++) {
                $dt = date("Y-m-d\TH:i:s", $ar[$i]['bankdate']);
                try {
                    if ($ar[$i]['tip'] == 0) {
                        /*
                          $res = $client->createPayment(array('PaymentRequest' =>
                          array('id_user' => 2,
                          'payment' => array(
                          'contract_code' => $ar[$i]['account'],
                          'date_cr' => $dt,
                          'date_info' => date("Y-m-d\TH:i:s"),
                          'description' => $ar[$i]['comment'],
                          'ident' => $ar[$i]['ident'],
                          'status' => 'A',
                          'sum' => $ar[$i]['summa']),
                          'paymentSystem' => $ar[$i]['bank_id']))
                          );
                         */
                    } else {

                        $res = $client->deletePayment(array('PaymentRequest' => array('id_user' => 2,
                                'payment' => array(
                                    'contract_code' => $ar[$i]['account'],
                                    'ident' => $ar[$i]['ident'],
                                    'date_del' => date("Y-m-d\TH:i:s"),
                                    'description' => $ar[$i]['comment'],
                                    'sum' => $ar[$i]['summa']),
                                'paymentSystem' => $ar[$i]['bank_id'])));
                    }
//  print "Send :\n".htmlspecialchars($client->__getLastRequest()) ."\n"; 
//   if (isset($res->return->requestStatus->id) and $res->return->requestStatus->id!=0)
                    if (isset($res->return->requestStatus->id) and
                            is_numeric($res->return->requestStatus->id)) {
                        $out[$j]['id_pay'] = $res->return->requestStatus->id;

                        $out[$j]['descr'] = iconv('UTF-8', 'windows-1251', $res->return->requestStatus->name);
                        $out[$j]['id'] = $ar[$i]['id'];
                        $out[$j]['trandate'] = date("Y-m-d H:i:s");
                        echo 'update data: ' . print_r($out, true);
                        $j++;
                    } else {
                        echo('Error: reply is null : ' . print_r($ar[$i], true));
                    }
                } catch (Exception $e) {
                    echo("ERROR\n" . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            echo("Нет коннекта - {$e->getMessage()}");
        }
    }
    return $out;
}

/*
  $rq[] = Array(
  'id' => 5878247,
  'account' => '12058282',
  'summa' => 39500,
  'trandate' => 1349331019,
  'bankdate' => 1349245800,
  'comment' => 'Сторно - ERIP',
  'bank' => 'ERIP',
  'bank_id' => '8763',
  'ident' => '8763_12058282_183546403224420121003093000_795',
  'id_pay' => 999999,
  'tip' => 2
  );
 */
$rq[] = Array(
    'id' => 5878247,
    'account' => '12058282',
    'summa' => 39500,
    'trandate' => 1349331019,
    'bankdate' => 1349245800,
    'comment' => 'Сторно - ERIP',
    'bank' => 'ERIP',
    'bank_id' => 'AKADO_EXCHANGE',
    'ident' => 'xxcx',
    'id_pay' => 999999,
    'tip' => 2
);


ar2billing($rq);
?>
