<?php
/**
 * Created by NextPay.ir.
 * User: NextPay Developer Team
 * Date: 2/20/17
 * Time: 12:56 PM
 */

header('Content-Type: text/html; charset=utf-8');

ini_set("display_errors", 1);
error_reporting(E_ALL);

$api_key = "44448888-3333-4323-2222-111111111111";
$trans_id = isset($_POST['trans_id']) ? $_POST['trans_id'] : false ;
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : time() ;
$amount = isset($_POST['amount']) ? $_POST['amount'] : 100 ;
$callback_uri = isset($_POST['callback_url']) ? $_POST['callback_url'] : 'http://localhost' ;


if(isset($_POST['start_pay'])){
    include_once "nextpay_payment.php";
    $parameters = array(
        'api_key' 	=> $api_key,
        'amount' 		=> $amount,
        'callback_uri' 	=> $callback_uri,
        'order_id' 		=> $order_id
    );

    try {
        $nextpay = new Nextpay_Payment($parameters);
        $nextpay->setDefaultVerify(Type_Verify::SoapClient);
        $result = $nextpay->token();
        if(intval($result->code) == -1){
            $nextpay->send($result->trans_id);
        }
        else
        {
            $message = ' شماره خطا: '.$result->code.'<br />';
            $message .='<br>'.$nextpay->code_error(intval($result->code));
            echo $message;
            exit();
        }
    }catch (Exception $e) { echo 'Error'. $e->getMessage();  }
}
elseif( $trans_id && $order_id ){
    include_once "nextpay_payment.php";
    $nextpay = new Nextpay_Payment();
    if (!is_string($trans_id) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $trans_id) !== 1)) {
        $message = ' شماره خطا: -34 <br />';
        $message .='<br>'.$nextpay->code_error(intval(-34));
        echo $message;
        exit();
    }
    $parameters = array
    (
        'api_key'	=> $api_key,
        'order_id'	=> $order_id,
        'trans_id' 	=> $trans_id,
        'amount'	=> $amount,
    );
    try {
        $result = $nextpay->verify_request($parameters);
        if( $result < 0 ) {
            $message ='<br>پرداخت موفق نبوده است';
            $message .='<br>شماره تراکنش : <span>' . $trans_id .'</span><br>';
            $message = ' شماره خطا: ' . $result . ' <br />';
            $message .='<br>'.$nextpay->code_error(intval($result));
            echo $message;
            exit();
        } elseif ($result==0) {
            $message ='<br>پرداخت موفق است';
            $message .='<br>شماره تراکنش : <span>' . $trans_id .'</span><br>';
            $message .='<br>شماره پیگیری : <span>' . $order_id .'</span><br>';
            $message .='<br>مبلغ : ' . $amount .'<br>';
            echo $message;
            exit();
        }else{
            $message ='<br>پرداخت موفق نبوده است';
            $message .='<br>شماره تراکنش : ' . $trans_id .'<br>';
            echo $message;
            exit();
        }
    }catch (Exception $e) { echo 'Error'. $e->getMessage();  }
}
else{
    ?>
    NextPay Payment Gateway:
    <hr>
    <form action="index.php" method="post" >
        <table>
            <tr>
                <th>Amount</th>
                <td><input type="text" name="amount" value="100" /> * Toman</td>
            </tr>
            <tr>
                <th>Order ID</th>
                <td><input type="text" name="order_id" value="12345" />*</td>
            </tr>
            <tr>
                <th>Callback URL</th>
                <td><input type="text" name="callback_url" value="http://localhost" /></td>
            </tr>
            <tr>
                <th></th>
                <td><input type="submit" name="start_pay" value="pay now" /></td>
            </tr>
        </table>
    </form>
<?php } ?>

