<?php
/**
 * Created by NextPay.ir.
 * User: NextPay Developer Team
 * Date: 07/17/2019
 * Time: 11:56 AM
 */
ini_set("display_errors", 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Tehran');
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>نمونه کد با  استفاده از کلاس php</title>
	<style type="text/css">
        .form-style-2{
            max-width: 500px;
            padding: 20px 12px 10px 20px;
            font: 13px Arial, Helvetica, sans-serif;
        }
        .form-style-2-heading{
            font-weight: bold;
            font-style: italic;
            border-bottom: 2px solid #ddd;
            margin-bottom: 20px;
            font-size: 15px;
            padding-bottom: 3px;
        }
        .form-style-2 label{
            display: block;
            margin: 0px 0px 15px 0px;
        }
        .form-style-2 label > span{
            width: 400px;
            font-weight: bold;
            float: right;
            padding-top: 8px;
            padding-right: 5px;
        }
    </style>
</head>
<body dir="rtl">
	
    <div class="form-style-2">
        <div class="form-style-2-heading">NextPay Payment Gateway:</div>
        <hr>
        <?php
        $trans_id    = isset($_POST['trans_id'])    ? $_POST['trans_id']    : false ;
        $order_id    = isset($_POST['order_id'])    ? $_POST['order_id']    : false ;
        $amount      = isset($_POST['amount'])      ? $_POST['amount']      : 100;
        $api_key     = isset($_POST['api_key'])     ? $_POST['api_key']     : "44448888-3333-4323-2222-111111111111";
        $refund_key  = isset($_POST['refund_key'])  ? $_POST['refund_key']  : "" ;

        if( $trans_id && $order_id ) {
            
            include_once "nextpay_payment.php";
            $nextpay = new Nextpay_Payment();
            $nextpay->setDefaultMethod(Type_Method_Payment::SoapClient);
            
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
                'refund_key'=> $refund_key
            );
            
            try {
                $result = $nextpay->refund_request($parameters);
                if( $result < -90 ) {
                    $message = "
                            <label>
                                <span>شماره سفارش : <span>$order_id</span></span>
                            </label>
                            <label>
                                <span>شماره تراکنش : <span>$trans_id</span></span>
                            </label>
                            <label>
                                <span>مبلغ : <span> $amount</span></span>
                            </label>
                            <label>
                                <span>وضعیت تراکنش : <span style='color:red'>هنوز موفق</span></span>
                            </label>
                            <lable>
                                <span onclick='refund()' style='color:red'>ارسال دوباره بازگشت پرداخت</span>
                            </lable>
                            <label>
                                <span>شماره خطا : <span>$result</span></span>
                            </label>";
                    $message .= '<label><span>توضیح خطا : <span>' . $nextpay->code_error(intval($result)) . '</span></span></label>"';
                    echo $message;
                } elseif ($result==-90) {
                    $message = "
                            <label>
                                <span>شماره سفارش : <span>$order_id</span></span> 
                            </label>
                            <label>
                                <span>شماره تراکنش : <span>$trans_id</span></span> 
                            </label>
                            <label>
                                <span>مبلغ : <span> $amount</span></span> 
                            </label>
                            <label>
                                <span>وضعیت بازگشت مبلغ : <span style='color:green'>با موفقیت بازگشت داده شد</span></span> 
                            </label>
                            <lable>
                                <span onclick='payment()' style='color:red'>خرید دوباره</span>
                            </lable>";
                    echo $message;
                }else{
                    $message = "
                            <label>
                                <span>شماره سفارش : <span>$order_id</span></span>
                            </label>
                            <label>
                                <span>شماره تراکنش : <span>$trans_id</span></span>
                            </label>
                            <label>
                                <span>مبلغ : <span> $amount</span></span>
                            </label>
                            <label>
                                <span>وضعیت تراکنش : <span style='color:red'>هنوز موفق</span></span>
                            </label>
                            <lable>
                                <span onclick='refund()' style='color:red'>ارسال دوباره بازگشت پرداخت</span>
                            </lable>
                            <label>
                                <span>شماره خطا : <span>$result</span></span>
                            </label>";
                    $message .= '<label><span>توضیح خطا : <span>' . $nextpay->code_error(intval($result)) . '</span></span></label>"';
                    echo $message;
                }
            }catch (Exception $e) { echo 'Error'. $e->getMessage();  }
        }
        ?>
    </div>
    <script language="javascript" type="text/javascript">
        function payment () {
            var form = document.createElement("form");
            form.setAttribute("method", "GET");
            form.setAttribute("action", "index.php");
            form.setAttribute("target", "_self");
            form.style.visibility = 'hidden';

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
        function refund () {
            var form = document.createElement("form");
            form.setAttribute("method", "POST");
            form.setAttribute("action", "refund.php");
            form.setAttribute("target", "_self");
            var trans_id = document.createElement("input");
            trans_id.setAttribute("name", "trans_id");
            trans_id.setAttribute("value", "<?php echo $trans_id ?>");
            form.appendChild(trans_id);
            var order_id = document.createElement("input");
            order_id.setAttribute("name", "order_id");
            order_id.setAttribute("value", "<?php echo $order_id ?>");
            form.appendChild(order_id);
            var api_key = document.createElement("input");
            api_key.setAttribute("name", "api_key");
            api_key.setAttribute("value", "<?php echo $api_key ?>");
            form.appendChild(api_key);
            var amount = document.createElement("input");
            amount.setAttribute("name", "amount");
            amount.setAttribute("value", "<?php echo $amount ?>");
            form.appendChild(amount);            
            var refund_key = document.createElement("input");
            refund_key.setAttribute("name", "refund_key");
            refund_key.setAttribute("value", "<?php echo $refund_key ?>");
            form.appendChild(refund_key);
            
            form.style.visibility = 'hidden';

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    </script>
</body>
</html>

