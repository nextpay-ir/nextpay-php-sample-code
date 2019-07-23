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
        width: 200px;
        font-weight: bold;
        float: right;
        padding-top: 8px;
        padding-right: 5px;
    }
    .form-style-2 span.required{
        color:red;
    }
    .form-style-2 .tel-number-field{
        width: 40px;
        text-align: center;
    }
    .form-style-2 input.input-field, .form-style-2 .select-field{
        width: 55%;	
    }
    .form-style-2 input.input-field, 
    .form-style-2 .tel-number-field, 
    .form-style-2 .textarea-field, 
    .form-style-2 .select-field{
        box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        border: 1px solid #C2C2C2;
        box-shadow: 1px 1px 4px #EBEBEB;
        -moz-box-shadow: 1px 1px 4px #EBEBEB;
        -webkit-box-shadow: 1px 1px 4px #EBEBEB;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        padding: 7px;
        outline: none;
    }
    .form-style-2 .input-field:focus, 
    .form-style-2 .tel-number-field:focus, 
    .form-style-2 .textarea-field:focus,  
    .form-style-2 .select-field:focus{
        border: 1px solid #0C0;
    }
    .form-style-2 .textarea-field{
        height:100px;
        width: 55%;
    }
    .form-style-2 input[type=submit],
    .form-style-2 input[type=button]{
        border: none;
        padding: 8px 15px 8px 15px;
        background: #FF8500;
        color: #fff;
        box-shadow: 1px 1px 4px #DADADA;
        -moz-box-shadow: 1px 1px 4px #DADADA;
        -webkit-box-shadow: 1px 1px 4px #DADADA;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
    }
    .form-style-2 input[type=submit]:hover,
    .form-style-2 input[type=button]:hover{
        background: #EA7B00;
        color: #fff;
    }
    </style>
    <script language="javascript" type="text/javascript">
        function payment () {
            var form = document.createElement("form");
            form.setAttribute("method", "GET");
            form.setAttribute("action", "");
            form.setAttribute("target", "_self");
            form.style.visibility = 'hidden';

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    </script>
</head>
<body dir="rtl">

<?php
$api_key      = isset($_POST['api_key'])      ? $_POST['api_key']      : "44448888-3333-4323-2222-111111111111";
$order_id     = isset($_POST['order_id'])     ? $_POST['order_id']     : time() ;
$amount       = isset($_POST['amount'])       ? $_POST['amount']       : 100 ; // amount [use in refund and custom field]
$callback_uri = isset($_POST['callback_uri']) ? $_POST['callback_uri'] : 'http://localhost/refund.php' ;
$refund_key   = isset($_POST['refund_key'])   ? $_POST['refund_key']   : '' ;

if(substr($callback_uri, 0, 7) != "http://" || substr($callback_uri, 0, 7) != "https://") $callback_uri = "http://". $callback_uri;

if(array_key_exists('api_key', $_POST) && isset($_POST['api_key'])){
    include_once "nextpay_payment.php";
    $parameters = array(
        'api_key'       => $api_key,
        'amount'        => $amount,
        'callback_uri'  => $callback_uri,
        'order_id'      => $order_id,
        "custom"        => json_encode(array("amount"=>$amount, "id"=> "test-id-123", "api_key"=>$api_key))
    );
    
    if(array_key_exists('refund_key', $_POST) && isset($_POST['refund_key'])){
        $parameters["refund_key"] = $_POST['refund_key'];
        $parameters["custom"] = json_encode(array("amount"=>$amount, "refund_key"=>$_POST['refund_key'], "id"=> "test-id-123", "api_key"=>$api_key));
    }

    try {
        $nextpay = new Nextpay_Payment($parameters);
        $nextpay->setDefaultMethod(Type_Method_Payment::SoapClient);
        $result = $nextpay->token();
        if(intval($result->code) == -1){
            $nextpay->send($result->trans_id);
        }
        else
        {
            $message = ' شماره خطا: '.$result->code.'<br />';
            $message .='<br>'.$nextpay->code_error(intval($result->code));
        ?>
            <div class="form-style-2">
                <div class="form-style-2-heading">NextPay Payment Gateway:</div>
                    <lable>
                        <span onclick='payment()' style='color:red'>خرید دوباره</span>
                    </lable>
                    <p>
                    <?php echo $message; ?>
                    </p>
                </div>
            </div>
        <?php            
            exit();
        }
    }catch (Exception $e) { echo 'Error'. $e->getMessage();  }
}else{
?>    
    <div class="form-style-2">
        <div class="form-style-2-heading">NextPay Payment Gateway:</div>
        <form action="" method="post">
            <label for="api_key">
                <span>کلید مجوز دهی <span class="required">*</span></span>
                <input type="text" class="input-field" placeholder="کلید مجوز دهی" name="api_key" value="" />
            </label>
            <label for="order_id">
                <span>شماره سفارش <span class="required">*</span></span>
                <input type="text" class="input-field" placeholder="شماره سفارش" name="order_id" value="" />
            </label>
            <label for="amount">
                <span>مبلغ <span class="required">*</span></span>
                <input type="text" class="input-field" placeholder="مبلغ" name="amount" value="" />
            </label>
            <label for="callback_uri">
                <span>آدرس بازگشت <span class="required">*</span></span>
                <input type="text" class="input-field" placeholder="آدرس بازگشت" name="callback_uri" value="" />
            </label>
            <label for="refund_key">
                <span>کلید بازگشت مبلغ</span>
                <input type="text" class="input-field" placeholder="کلید بازگشت مبلغ (بصورت اختیاری)" name="refund_key" value="" />
            </label>
            <label>
                <span> </span>
                <input type="submit" value="پرداخت" />
            </label>
        </form>
    </div>

    <p></p>
<?php } ?>
</body>
</html>
