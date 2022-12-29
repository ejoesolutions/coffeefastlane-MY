<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Billplz\Minisite\API;
use Billplz\Minisite\Connect;

class Orders extends CI_Controller{

  public function __construct()
  {
    parent::__construct();
    $this->load->library('email');
    $this->load->model('order_model');
    $this->load->model('customer_model');
    $this->load->model('admin_model');

    if (!$this->ion_auth->logged_in())
		{
			redirect('customer/register', 'refresh');
		} else {
      $user = $this->ion_auth->user()->row();
      $this->data['user_profile'] = $this->user_model->get_profile($user->id);
      $this->data['shop'] = $this->user_model->get_seller($user->id);
      if($this->data['user_profile']['user_group']==2){
        $this->data['count_order'] = $this->order_model->get_order_status(null,$this->data['shop']['seller_id']);
      }else {
        $this->data['count_order'] = $this->order_model->get_order_status();
      }
      //$this->data['address'] = $this->customer_model->get_addresses($user->id);
    }
    //$this->data['metal'] = $this->product_model->get_gs_price();
    $this->data['logo'] = $this->admin_model->get_logo();
    $this->data['pm'] = $this->product_model->get_productCategory();
    $this->data['footer'] = $this->admin_model->get_footer();
    //$this->data['count_order'] = $this->order_model->count_order();
    $this->data['count_newSeller'] = $this->user_model->count_register_seller(); //utk side menu
    $this->data['state']=$this->user_model->state_list();
    $this->data['list_shop'] = $this->product_model->list_seller();
    $this->output->enable_profiler(false);
  }
  public function index()
  {
    $this->orders();
  }

  public function checkout(){
    if (!$this->ion_auth->logged_in())
    {
      redirect('customer/register', 'refresh');
    } else {
      $user = $this->ion_auth->user()->row();
      $this->data['user_profile'] = $this->user_model->get_profile($user->id);
      //$this->data['bill'] = $this->customer_model->bill_address($user->id);
      $this->data['ship'] = $this->customer_model->ship_address($user->id);
      $this->data['ship_state'] = $this->user_model->state($this->data['ship']['ship_state']);
      $this->data['state'] = $this->user_model->state_list();
      $this->data['temp_state'] = $this->user_model->state();
      $this->data['cost'] = $this->order_model->get_weightcost();
      $this->data['seller'] = $this->user_model->get_seller();
      $this->template->load('layouts/main','orders/order_checkout', $this->data);
    }

  }


  public function store_order_fpx()
  {

    include_once("Billplz/lib/API.php");
    include_once("Billplz/lib/Connect.php");
    include_once("Billplz/configuration.php");
    //session_start();
    if(isset($_POST['btnPlaceOrder'])){

      $_SESSION['payments']=$payment_type=$this->input->post('payments');
      $_SESSION['full_name']=$full_name=$this->input->post('full_name');
      $_SESSION['email']=$email=$this->input->post('email');
      $_SESSION['phone']=$phone=$this->input->post('phone');
      $_SESSION['total_all']=$total_all=$this->input->post('total_all');
      $_SESSION['total_weight']=$total_weight=$this->input->post('total_weight');
      $_SESSION['total_shipping']=$total_shipping=$this->input->post('total_shipping');
      //$_SESSION['weightcost_id']=$weightcost_id=$this->input->post('weightcost_id');

      $_SESSION['arr_itemID']=$arr_itemID = $this->input->post('item_id[]')? $this->input->post('item_id[]') : NULL;
      $_SESSION['arr_itemName']=$arr_itemName = $this->input->post('item_name[]')? $this->input->post('item_name[]') : NULL;
      $_SESSION['arr_itemQty']=$arr_itemQty = $this->input->post('item_qty[]')? $this->input->post('item_qty[]') : NULL;
      $_SESSION['arr_itemPrice']=$arr_itemPrice = $this->input->post('item_price[]')? $this->input->post('item_price[]') : NULL;
      $_SESSION['arr_itemSubtotal']=$arr_itemSubtotal = $this->input->post('item_subtotal[]')? $this->input->post('item_subtotal[]') : NULL;
      $_SESSION['arr_itemModal']=$arr_itemModal = $this->input->post('modal[]')? $this->input->post('modal[]') : NULL;
      $_SESSION['arr_itemUnitPrice']=$arr_itemUnitPrice = $this->input->post('unit_price[]')? $this->input->post('unit_price[]') : NULL;
      $_SESSION['arr_itemTaxPrice']=$arr_itemTaxPrice = $this->input->post('tax_price[]')? $this->input->post('tax_price[]') : NULL;
      $_SESSION['arr_itemShipPrice']=$arr_itemShipPrice = $this->input->post('ship_price[]')? $this->input->post('ship_price[]') : NULL;
      $_SESSION['arr_seller_id']=$arr_itemShipPrice = $this->input->post('seller_id[]')? $this->input->post('seller_id[]') : NULL;

        $_SESSION['sub_sellerID']=$sub_sellerID=$this->input->post('sub_seller_id');
      $_SESSION['sub_weightSeller']=$sub_weightSeller=$this->input->post('sub_weight_seller');
      $_SESSION['sub_shipping']=$sub_shipping=$this->input->post('sub_shipping');

        $_SESSION['user_id']=$this->input->post('user_id');
        // $_SESSION['bill_name'] = $this->input->post('bill_name');
        // $_SESSION['bill_email'] = $this->input->post('bill_email');
        // $_SESSION['bill_address']= $this->input->post('bill_address');
        // $_SESSION['bill_phone']= $this->input->post('bill_phone');

        $_SESSION['ship_name'] = strtoupper($this->input->post('ship_name'));
        $_SESSION['ship_address']= strtoupper($this->input->post('ship_address'));
        $_SESSION['ship_postcode']= $this->input->post('ship_postcode');
        $_SESSION['ship_area']= strtoupper($this->input->post('ship_area'));
        $_SESSION['ship_state']= $this->input->post('state_id');
        $_SESSION['ship_phone']= $this->input->post('ship_phone');


      if($payment_type=="Online Banking"){

        //OPEN NEW CODE
        $order = array(
          'created_by' => $_SESSION['user_id'],
          'created_date'=>date("Y-m-d H:i:s"),
          'total_weight'=>$_SESSION['total_weight'],
          'total_all'=>$_SESSION['total_all'],
          'total_shipping'=>$_SESSION['total_shipping'],
        );
        $this->db->insert('ci_orders', $order);
        $order_id = $this->db->insert_id();

          $ship = array(
            'user_id'=> $_SESSION['user_id'],
            'order_id'=> $order_id,
            'ship_name' => $_SESSION['ship_name'],
            'ship_address'=> $_SESSION['ship_address'],
            'ship_postcode'=> $_SESSION['ship_postcode'],
            'ship_area'=> $_SESSION['ship_area'],
            'ship_state'=> $_SESSION['ship_state'],
            'ship_phone'=> $_SESSION['ship_phone']
          );
        $this->db->insert('ci_shipping',$ship);

        for($i=0;$i<count($_SESSION['arr_itemID']);$i++){
          $items = array(
            'order_id'=>$order_id,
            'product_id' => $_SESSION['arr_itemID'][$i],
            'seller_id' => $_SESSION['arr_seller_id'][$i],
            'qty' => $_SESSION['arr_itemQty'][$i],
            'ordered_price' => $_SESSION['arr_itemPrice'][$i],
             'modal_price' => $_SESSION['arr_itemModal'][$i],
             'unit_price' => $_SESSION['arr_itemUnitPrice'][$i],
            'tax_price' => $_SESSION['arr_itemTaxPrice'][$i],
            'ship_price' => $_SESSION['arr_itemShipPrice'][$i],
            'subtotal' => $_SESSION['arr_itemSubtotal'][$i],
            'updated'=>date("Y-m-d H:i:s")
          );
          $this->db->insert('ci_order_items', $items);
        }

        $new_sellerID=array_values(array_unique($_SESSION['arr_seller_id']));
        for ($f=0; $f < count($new_sellerID) ; $f++) {
          $this->data['seller']=$this->user_model->get_detail_seller($new_sellerID[$f]);
          for($s=0;$s< count($_SESSION['sub_sellerID']);$s++){
              if($_SESSION['sub_sellerID'][$s]==$new_sellerID[$f]){
                $order_status=array(
                  'seller_id'=>$new_sellerID[$f],
                  'order_id'=>$order_id,
                  'order_status'=>0,
                  'shipping_seller'=>$_SESSION['sub_shipping'][$s]
                );
                $this->db->insert('ci_order_status', $order_status);
                $order_statusID=$this->db->insert_id();

                $trans=array(
                  'order_status_id'=>$order_statusID,
                  'transaction_status'=>0,
                  'transaction_record'=>date("Y-m-d H:i:s"),
                  'next_transaction'=>date("Y-m-d H:i:s",strtotime('+ 1 days')) //ship within 3 days
                );
                $this->db->insert('ci_transaction',$trans);
              }
          }
        }

        $payment = array(
          'order_id' => $order_id,
          'payment_type' =>$_SESSION['payments'],
          'recorded_date'=>date("Y-m-d H:i:s"),
          'payment_amount'=>$_SESSION['total_all']
        );
        $this->db->insert('ci_payment', $payment);
        //CLOSE NEW CODE

        $parameter = array(
            'collection_id' => !empty($collection_id) ? $collection_id : $_REQUEST['collection_id'],
            'email'=> $email,
            'mobile'=> $phone,
            'name'=> $full_name,
            'amount'=> $total_all*100,
            'callback_url'=> base_url() . 'orders/store_order_fpx',
            'description'=> !empty($description) ? $description : $_REQUEST['description']
        );

        $optional = array(
            'redirect_url' => base_url() . 'orders/store_order_fpx',
            'reference_1_label' => isset($reference_1_label) ? $reference_1_label : $_REQUEST['reference_1_label'],
            'reference_1' => $order_id,
            // 'reference_2_label' => isset($reference_2_label) ? $reference_2_label : $_REQUEST['reference_2_label'],
            // 'reference_2' => $total_all,
            'deliver' => 'false'
        );

        $connnect = (new Connect($api_key))->detectMode();
        $billplz = new API($connnect);
        list ($rheader, $rbody) = $billplz->toArray($billplz->createBill($parameter, $optional));
        if ($rheader !== 200) {

            $m="Payment not succesful";
            // echo "<script type='text/javascript'>alert('$m');</script>";
            // redirect(base_url('orders/checkout'), 'refresh');
            echo "<script type='text/javascript'>alert('$m');window.location.href = '" . base_url() . "orders/checkout';</script>";
        }else{
          //$this->cart->destroy();
          //NEW CODE
        $this->db->where('order_id',$rbody['reference_1']);
        $this->db->update('ci_payment',array('bill_id'=>$rbody['id']));
        //CLOSE
          header('Location: ' . $rbody['url']);
        }
      }else if($payment_type=="Cash Deposit"){
        $this->data['buyer']=$this->user_model->get_profile($_SESSION['user_id']);

        $order = array(
          //'type' => '1',//1=pesanan
          'created_by' => $_SESSION['user_id'],
          'created_date'=>date("Y-m-d H:i:s"),
          'total_weight'=>$_SESSION['total_weight'],
          'total_all'=>$_SESSION['total_all'],
          'total_shipping'=>$_SESSION['total_shipping'],
          //'weightcost_id'=>$_SESSION['weightcost_id'],
        );
        $this->db->insert('ci_orders', $order);

        $order_id = $this->db->insert_id();
        // $bill = array(
        //     'user_id'=> $_SESSION['user_id'],
        //     'order_id'=> $order_id,
        //     'bill_name' => $_SESSION['bill_name'],
        //     'bill_email' => $_SESSION['bill_email'],
        //     'bill_address'=> $_SESSION['bill_address'],
        //     'bill_phone'=> $_SESSION['bill_phone']
        //   );
        // $this->db->insert('ci_billing',$bill);

        $ship = array(
            'user_id'=> $_SESSION['user_id'],
            'order_id'=> $order_id,
            'ship_name' => $_SESSION['ship_name'],
            'ship_address'=> $_SESSION['ship_address'],
            'ship_postcode'=> $_SESSION['ship_postcode'],
            'ship_area'=> $_SESSION['ship_area'],
            'ship_state'=> $_SESSION['ship_state'],
            'ship_phone'=> $_SESSION['ship_phone']
          );
        $this->db->insert('ci_shipping',$ship);

        for($i=0;$i<count($_SESSION['arr_itemID']);$i++){
          $items = array(
            'order_id'=>$order_id,
            'product_id' => $_SESSION['arr_itemID'][$i],
            'seller_id' => $_SESSION['arr_seller_id'][$i],
            'qty' => $_SESSION['arr_itemQty'][$i],
            'ordered_price' => $_SESSION['arr_itemPrice'][$i],
             'modal_price' => $_SESSION['arr_itemModal'][$i],
             'unit_price' => $_SESSION['arr_itemUnitPrice'][$i],
            'tax_price' => $_SESSION['arr_itemTaxPrice'][$i],
            'ship_price' => $_SESSION['arr_itemShipPrice'][$i],
            'subtotal' => $_SESSION['arr_itemSubtotal'][$i],
            'updated'=>date("Y-m-d H:i:s")
          );
          $this->db->insert('ci_order_items', $items);

        }

        $new_sellerID=array_values(array_unique($_SESSION['arr_seller_id']));
        for ($f=0; $f < count($new_sellerID) ; $f++) {
          $this->data['seller']=$this->user_model->get_detail_seller($new_sellerID[$f]);
          for($s=0;$s< count($_SESSION['sub_sellerID']);$s++){
              if($_SESSION['sub_sellerID'][$s]==$new_sellerID[$f]){
                $order_status=array(
                  'seller_id'=>$new_sellerID[$f],
                  'order_id'=>$order_id,
                  'order_status'=>0,
                  'shipping_seller'=>$_SESSION['sub_shipping'][$s]
                );
                $this->db->insert('ci_order_status', $order_status);
                $order_statusID=$this->db->insert_id();

                $trans=array(
                  'order_status_id'=>$order_statusID,
                  'transaction_status'=>0,
                  'transaction_record'=>date("Y-m-d H:i:s"),
                  'next_transaction'=>date("Y-m-d H:i:s",strtotime('+ 1 days')) //bayar dlm masa 24H, lebih cancel
                );
                $this->db->insert('ci_transaction',$trans);
              }
          }
        }

        $payment = array(
          'order_id' => $order_id,
          'payment_type' =>$_SESSION['payments'],
          'recorded_date'=>date("Y-m-d H:i:s"),
        );
        $this->db->insert('ci_payment', $payment);

        $this->cart->destroy();

        //$this->load->library('email');

          //send email to buyer
          $subject = "Pesanan berjaya dibuat";
          $message = '<html><body>';
          $message .= "Assalamualaikum dan Selamat Sejahtera.<br><br>Pesanan anda bernombor #".$order_id." telah berjaya direkod. Sila buat pembayaran sebelum ".date("Y-m-d H:i:s",strtotime('+ 1 days'))." untuk proses selanjutnya atau pesanan anda akan dibatalkan.";
          $message .= "<br><br>Terima kasih.";
          $message .= "</body></html>";

          $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
          $this->email->to($this->data['buyer']['email']);
          $this->email->subject($subject);
          $this->email->message($message);
          $this->email->send();

        $m="Please make payment in 24 hours after order created or your order will be cancel automatically. Upload the receipt after make payment.";
        //$m=json_encode($new_sellerID);
        // echo "<script type='text/javascript'>alert('$m');</script>";
        // redirect('customer/orders', 'refresh');
        echo "<script type='text/javascript'>alert('$m');window.location.href = '" . base_url() . "customer/orders';</script>";

      }
    }else{
      $this->data['buyer']=$this->user_model->get_profile($_SESSION['user_id']);

      $connnect = (new Connect($api_key))->detectMode();
      $billplz = new API($connnect);
      list($rheader, $rbody)=$billplz->toArray($billplz->getBill($_GET['billplz']['id']));
      list($rheader1, $rbody1)=$billplz->toArray($billplz->getTransactionIndex($_GET['billplz']['id']));
      if($rbody['state']=="due"){
        list($rheader, $rbody)=$billplz->toArray($billplz->deleteBill($_GET['billplz']['id']));

        $m="Payment not successful";
        // echo "<script type='text/javascript'>alert('$m');</script>";
        // redirect('orders/checkout', 'refresh');
        echo "<script type='text/javascript'>alert('$m');window.location.href = '" . base_url() . "orders/checkout';</script>";

      }
      if($rbody['state']=="paid"){
        $this->cart->destroy();

        //OPEN NEW CODE
        $new_sellerID=array_values(array_unique($_SESSION['arr_seller_id']));
        for ($f=0; $f < count($new_sellerID) ; $f++) {
          $this->data['seller']=$this->user_model->get_detail_seller($new_sellerID[$f]);
          $this->data['order_status'] = $this->order_model->get_order_status($rbody['reference_1'],$new_sellerID[$f]);
          for($s=0;$s< count($_SESSION['sub_sellerID']);$s++){
              if($_SESSION['sub_sellerID'][$s]==$new_sellerID[$f]){
                $order_status=array(
                  'order_status'=>1,
                );
                $this->db->where('order_id',$rbody['reference_1']);
                $this->db->update('ci_order_status', $order_status);

                foreach ($this->data['order_status'] as $key) {
                  $trans=array(
                    'order_status_id'=>$key['order_status_id'],
                    'transaction_status'=>1,
                    'transaction_record'=>date("Y-m-d H:i:s"),
                    'next_transaction'=>date("Y-m-d H:i:s",strtotime('+ 3 days')) //ship within 3 days
                  );
                  $this->db->insert('ci_transaction',$trans);
                }

                //send email to seller
                $seller_email=array(
                  'subject'=>"Pesanan baru berjaya dibuat",
                  'message'=>"Assalamualaikum dan Selamat Sejahtera.<br><br>Pesanan bernombor #".$rbody['reference_1']." telah berjaya dibuat dan pembayaran telah disahkan. Anda diminta untuk menguruskan proses penghantaran kepada pelanggan sebelum ".date('d-m-y',strtotime('+ 3 days'))." atau pesanan akan dibatalkan.",
                  'seller_email'=>$this->data['seller']['email'],
                  'seller_phone'=>$this->data['seller']['phone'],
                );
                $this->to_seller($seller_email);
              }
          }
        }


        for($i=0;$i<count($_SESSION['arr_itemID']);$i++){
            $accetp_items = array(
              'owner_id' => $this->data['user_profile']['id'],
              'qty' => $_SESSION['arr_itemQty'][$i],
              'product_id' => $_SESSION['arr_itemID'][$i],
              'owner_type' => 'customer',
              'created_date'=>date("Y-m-d H:i:s"),
              'order_id'=>$rbody['reference_1']
            );
            $this->db->insert('ci_inventory', $accetp_items);
            $remove_items = array(
              'owner_id' => $_SESSION['arr_seller_id'][$i],
              'qty' => '-'. $_SESSION['arr_itemQty'][$i],
              'product_id' => $_SESSION['arr_itemID'][$i],
              'owner_type' => 'company',
              'created_date'=>date("Y-m-d H:i:s"),
              'order_id'=>$rbody['reference_1']
            );
            $this->db->insert('ci_inventory', $remove_items);

        }

        $payment = array(
          'payment_date'=>date('Y-m-d H:i:s',strtotime($rbody['paid_at'])),
          'reference_note'=>'Bill ID : '.$rbody['id'].' / Transaction ID : '.$rbody1['transactions'][0]['id'],
          'recorded_date'=>date("Y-m-d H:i:s"),
        );
        $this->db->where('order_id',$rbody['reference_1']);
        $this->db->update('ci_payment', $payment);

        //CLOSE NEW CODE

        // $order = array(
        //   'created_by' => $_SESSION['user_id'],
        //   'created_date'=>date("Y-m-d H:i:s"),
        //   'total_weight'=>$_SESSION['total_weight'],
        //   'total_all'=>$_SESSION['total_all'],
        //   'total_shipping'=>$_SESSION['total_shipping'],
        // );
        // $this->db->insert('ci_orders', $order);
        // $order_id = $this->db->insert_id();
        //
        //   $ship = array(
        //     'user_id'=> $_SESSION['user_id'],
        //     'order_id'=> $order_id,
        //     'ship_name' => $_SESSION['ship_name'],
        //     'ship_address'=> $_SESSION['ship_address'],
        //     'ship_postcode'=> $_SESSION['ship_postcode'],
        //     'ship_area'=> $_SESSION['ship_area'],
        //     'ship_state'=> $_SESSION['ship_state'],
        //     'ship_phone'=> $_SESSION['ship_phone']
        //   );
        // $this->db->insert('ci_shipping',$ship);
        //
        // for($i=0;$i<count($_SESSION['arr_itemID']);$i++){
        //   $items = array(
        //     'order_id'=>$order_id,
        //     'product_id' => $_SESSION['arr_itemID'][$i],
        //     'seller_id' => $_SESSION['arr_seller_id'][$i],
        //     'qty' => $_SESSION['arr_itemQty'][$i],
        //     'ordered_price' => $_SESSION['arr_itemPrice'][$i],
        //      'modal_price' => $_SESSION['arr_itemModal'][$i],
        //      'unit_price' => $_SESSION['arr_itemUnitPrice'][$i],
        //     'tax_price' => $_SESSION['arr_itemTaxPrice'][$i],
        //     'ship_price' => $_SESSION['arr_itemShipPrice'][$i],
        //     'subtotal' => $_SESSION['arr_itemSubtotal'][$i],
        //     'updated'=>date("Y-m-d H:i:s")
        //   );
        //   $this->db->insert('ci_order_items', $items);
        // }
        //
        // $new_sellerID=array_values(array_unique($_SESSION['arr_seller_id']));
        // for ($f=0; $f < count($new_sellerID) ; $f++) {
        //   $this->data['seller']=$this->user_model->get_detail_seller($new_sellerID[$f]);
        //   for($s=0;$s< count($_SESSION['sub_sellerID']);$s++){
        //       if($_SESSION['sub_sellerID'][$s]==$new_sellerID[$f]){
        //         $order_status=array(
        //           'seller_id'=>$new_sellerID[$f],
        //           'order_id'=>$order_id,
        //           'order_status'=>1,
        //           'shipping_seller'=>$_SESSION['sub_shipping'][$s]
        //         );
        //         $this->db->insert('ci_order_status', $order_status);
        //         $order_statusID=$this->db->insert_id();
        //
        //         $trans=array(
        //           'order_status_id'=>$order_statusID,
        //           'transaction_status'=>1,
        //           'transaction_record'=>date("Y-m-d H:i:s"),
        //           'next_transaction'=>date("Y-m-d H:i:s",strtotime('+ 3 days')) //ship within 3 days
        //         );
        //         $this->db->insert('ci_transaction',$trans);
        //
        //         //send email to seller
        //         $seller_email=array(
        //           'subject'=>"Pesanan baru berjaya dibuat",
        //           'message'=>"Assalamualaikum dan Selamat Sejahtera.<br><br>Pesanan bernombor #".$order_id." telah berjaya dibuat dan pembayaran telah disahkan. Anda diminta untuk menguruskan proses penghantaran kepada pelanggan sebelum ".date('d-m-y',strtotime('+ 3 days'))." atau pesanan akan dibatalkan.",
        //           'seller_email'=>$this->data['seller']['email'],
        //           'seller_phone'=>$this->data['seller']['phone'],
        //         );
        //         $this->to_seller($seller_email);
        //       }
        //   }
        // }
        //
        // $payment = array(
        //   'order_id' => $order_id,
        //   'payment_type' =>$_SESSION['payments'],
        //   'reference_note'=>'Bill ID : '.$rbody['id'].' / Transaction ID : '.$rbody1['transactions'][0]['id'],
        //   'recorded_date'=>date("Y-m-d H:i:s"),
        // );
        // $this->db->insert('ci_payment', $payment);

        //send email to buyer
        $buyer_email=array(
          'subject'=>"Pesanan berjaya dihantar kepada penjual",
          'message'=>"Assalamualaikum dan Selamat Sejahtera.<br><br>Pesanan anda bernombor #".$rbody['reference_1']." telah berjaya dihantar kepada penjual. Sila tunggu untuk penerimaan barang pesanan.",
          'buyer_email'=>$this->data['buyer']['email'],
        );
        $this->to_buyer($buyer_email);

        $admin_email=array(
          'subject'=>"Pesanan berjaya dihantar kepada penjual",
          'message'=>"Assalamualaikum dan Selamat Sejahtera.<br><br>Pesanan bernombor #".$rbody['reference_1']." telah berjaya direkod dan bayaran telah dilakukan. Sila buat semakan selanjutnya.",

        );
        $this->to_admin($admin_email);

        $m="Payment succesful";
        // echo "<script type='text/javascript'>alert('$m');</script>";
        // redirect('customer/orders', 'refresh');
        echo "<script type='text/javascript'>alert('$m');window.location.href = '" . base_url() . "customer/orders';</script>";

      }
    }
      //redirect('customer/orders', 'refresh');
  }

  // NEW CODE OPEN
  public function store_pending_payment()
  {
    $order_status = $this->order_model->get_order_status($this->input->post('order_id'),null);
    $order_item = $this->order_model->get_items($this->input->post('order_id'));
    $order = $this->order_model->get_order(null,$this->input->post('order_id'));

    $data_pay=[
      'payment_date'=>date('Y-m-d',strtotime($this->input->post('pay_date'))),
      'reference_note'=>strtoupper($this->input->post('reference_note'))
    ];
    $this->db->where('order_id',$this->input->post('order_id'));
    $this->db->update('ci_payment',$data_pay);

    foreach ($order_status as $key) {
      $this->db->insert('ci_transaction',array('transaction_status'=>1,'transaction_record'=>date("Y-m-d H:i:s"),'next_transaction'=>date("Y-m-d H:i:s",strtotime('+ 3 days')),'order_status_id'=>$key['order_status_id']));
      //send email to seller
      $seller_email=array(
        'subject'=>"Pesanan baru berjaya dibuat",
        'message'=>"Assalamualaikum dan Selamat Sejahtera.<br><br>Pesanan bernombor #".$this->input->post('order_id')." telah berjaya dibuat dan pembayaran telah disahkan. Anda diminta untuk menguruskan proses penghantaran kepada pelanggan sebelum ".date('d-m-y',strtotime('+ 3 days'))." atau pesanan akan dibatalkan.",
        'seller_email'=>$key['email'],
        'seller_phone'=>$key['phone'],
      );
      $this->to_seller($seller_email);
    }

    $this->db->where('order_id',$this->input->post('order_id'));
    $this->db->update('ci_order_status',array('order_status'=>1));

    foreach ($order_item as $item){
        $accetp_items = array(
          'owner_id' => $item['user_id'],
          'qty' => $item['qty'],
          'product_id' => $item['product_id'],
          'owner_type' => 'customer',
          'created_date'=>date("Y-m-d H:i:s"),
          'order_id'=>$item['order_id']
        );
        $this->db->insert('ci_inventory', $accetp_items);
        $remove_items = array(
          'owner_id' => $item['seller_id'],
          'qty' => '-'. $item['qty'],
          'product_id' => $item['product_id'],
          'owner_type' => 'company',
          'created_date'=>date("Y-m-d H:i:s"),
          'order_id'=>$item['order_id']
        );
        $this->db->insert('ci_inventory', $remove_items);

    }

    //send email to buyer
    $buyer_email=array(
      'subject'=>"Pesanan berjaya dihantar kepada penjual",
      'message'=>"Assalamualaikum dan Selamat Sejahtera.<br><br>Pesanan anda bernombor #".$this->input->post('order_id')." telah berjaya dihantar kepada penjual. Sila tunggu untuk penerimaan barang pesanan.",
      'buyer_email'=>$order['email'],
    );
    $this->to_buyer($buyer_email);

    $admin_email=array(
      'subject'=>"Pesanan berjaya dihantar kepada penjual",
      'message'=>"Assalamualaikum dan Selamat Sejahtera.<br><br>Pesanan bernombor #".$this->input->post('order_id')." telah berjaya direkod dan bayaran telah dilakukan. Sila buat semakan selanjutnya.",

    );
    $this->to_admin($admin_email);

    redirect('orders/all_orders','refresh');
  }
  //CLOSE NEW CODE

  public function store_payment_receipt()
  {
    // Muat naik imej
    $config['upload_path'] = 'payment_receipt';
    $config['allowed_types']  = 'jpg|png|jpeg|pdf';
    // $config['max_width']  =  1500;
    // $config['max_height']  =  1500;
    $config['max_size'] = 500;
    $config['encrypt_name']  =  TRUE;
    $config['remove_spaces']  =  TRUE;
    $config['file_ext_tolower']  =  TRUE;
    $config['overwrite']  =  FALSE;

    $this->load->library('upload', $config);

    if ($this->upload->do_upload('att_file')) {
        $order_item = $this->order_model->get_items($this->input->post('order_id'));

        $upload_data = $this->upload->data();
        $image_file = $upload_data['raw_name'] . $upload_data['file_ext'];

        // Simpan imej dalam database
        $imageData = array(
          'att_file' => $image_file,
          'reference_note' => strtoupper($this->input->post('bank_name')),
          'recorded_date'=>date("Y-m-d H:i:s"),
        );
        $this->db->where('payment_id',$this->input->post('payment_id'));
        $this->db->update('ci_payment',$imageData);

        $this->db->where('order_id',$this->input->post('order_id'));
        $this->db->update('ci_order_status',array('order_status'=>1));

        $arr_order_status=$this->input->post('order_status_id[]');
        $arr_sellerID=$this->input->post('seller_id[]');
        for ($j=0;$j<count($arr_order_status);$j++) {
          $this->data['seller']=$this->user_model->get_detail_seller($arr_sellerID[$j]);
          $trans=array(
            'order_status_id'=>$arr_order_status[$j],
            'transaction_status'=>1,
            'transaction_record'=>date("Y-m-d H:i:s"),
            'next_transaction'=>date("Y-m-d H:i:s",strtotime('+ 3 days'))
          );
          $this->db->insert('ci_transaction',$trans);

          //send email to seller
          // $subject1 = "Bukti pembayaran pesanan bernombor #".$this->input->post('order_id');
          // $message1 = '<html><body>';
          // $message1 .= "Assalamualaikum dan Selamat Sejahtera.<br><br>Bayaran untuk pesanan bernombor #".$order_id." telah berjaya dibuat. Sila buat proses penghantaran barang kepada pelanggan.";
          // $message1 .= "<br><br>Terima kasih.";
          // $message1 .= "</body></html>";
          //
          // $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
          // $this->email->to($this->data['seller']['email']);
          // $this->email->subject($subject1);
          // $this->email->message($message1);
          // $this->email->send();
        }

        foreach ($order_item as $item){
        $accetp_items = array(
          'owner_id' => $item['user_id'],
          'qty' => $item['qty'],
          'product_id' => $item['product_id'],
          'owner_type' => 'customer',
          'created_date'=>date("Y-m-d H:i:s"),
          'order_id'=>$item['order_id']
        );
        $this->db->insert('ci_inventory', $accetp_items);
        $remove_items = array(
          'owner_id' => $item['seller_id'],
          'qty' => '-'. $item['qty'],
          'product_id' => $item['product_id'],
          'owner_type' => 'company',
          'created_date'=>date("Y-m-d H:i:s"),
          'order_id'=>$item['order_id']
        );
        $this->db->insert('ci_inventory', $remove_items);

    }

        $m="Upload successful.";
        // echo "<script type='text/javascript'>alert('$m');</script>";
        // redirect('customer/view_order/'.$this->input->post('order_id'), 'refresh');
         echo "<script type='text/javascript'>alert('$m');window.location.href = '" . base_url() . "customer/view_order/".$this->input->post('order_id')."';</script>";
    }else{
      $m="Upload unsuccessful.";
    //   echo "<script type='text/javascript'>alert('$m');</script>";
    //   redirect('customer/view_order/'.$this->input->post('order_id'), 'refresh');
       echo "<script type='text/javascript'>alert('$m');window.location.href = '" . base_url() . "customer/view_order/".$this->input->post('order_id')."';</script>";
    }
  }

  public function all_orders()
  {
    if (!$this->ion_auth->logged_in())
    {
      redirect('user/login', 'refresh');
    }else{

        $this->data['title'] = 'Orders';
        $this->data['orders'] = $this->order_model->get_order();
        if($this->data['shop']['seller_id']!='' && $this->data['user_profile']['user_group']==2){
          $this->data['items'] = $this->order_model->get_order_seller($this->data['shop']['seller_id']);
        }else{
          $this->data['items'] = $this->order_model->get_items();
        }
        $this->data['order_status'] = $this->order_model->get_order_status();
        //$this->data['items'] = $this->order_model->get_items();

        $this->template->load('layouts/admin','orders/orders_table', $this->data);
      }
    }


  public function detail($order_id,$seller_id,$no_siri='',$msg='')
  {
    if (!$this->ion_auth->logged_in())
    {
      redirect('user/login', 'refresh');
    }

      $this->data['title'] = 'Order Details';
      $this->data['seller_id'] = $seller_id;
      //$this->data['metal'] = $this->product_model->get_gs_price();
      $this->data['orders'] = $this->order_model->get_order(null,$order_id);
      $this->data['items'] = $this->order_model->get_items($order_id);
      $this->data['list_courier'] = $this->order_model->get_list_courier();
      $this->data['order_status'] = $this->order_model->get_order_status($order_id,$seller_id);
      if($no_siri){
        $this->data['no_siri']=$no_siri;
        $this->data['error_msg']='Kemungkinan no siri berikut telah digunakan/bertindih : '.json_encode($msg);

      }
      $this->data['pid']=$this->order_model->get_productID_2($order_id);
      //if($this->data['orders']['status']>=2){
      $this->data['siri']=$this->order_model->get_siri($order_id,$seller_id);
      $this->data['track']=$this->order_model->get_track($order_id,$seller_id);

      $this->data['due']=$this->admin_model->get_setting_day();//latest
      $this->data['release']=$this->order_model->get_release_pay($order_id,$seller_id);
      //}

      $this->template->load('layouts/admin','orders/order_detail', $this->data);
  }

  public function upd_release_pay()
  {
    $order_id=$this->input->post('order_id');
    $seller_id=$this->input->post('seller_id');
    $data=[
      'pay_amount'=>$this->input->post('pay_amount'),
      'ref_date'=>date('Y-m-d',strtotime($this->input->post('ref_date'))),
      'ref_note'=>nl2br(strtoupper($this->input->post('ref_note'))),
    ];

    $this->db->where('order_id',$order_id);
    $this->db->where('seller_id',$seller_id);
    $this->db->update('ci_release_payment',$data);

    redirect('orders/detail/'.$order_id.'/'.$seller_id,'refresh');
  }


  public function delete($order_id)
  {
    $this->db->where('order_id', $order_id);
    $this->db->where('status', 0);
    $this->db->delete('ci_orders');

    redirect('orders', 'refresh');
  }

  public function get_product()
  {
    $product_id = $this->input->post('product_id');
    //$this->data['metal'] = $this->product_model->get_gs_price();
    $this->data['product'] = $this->order_model->get_product($product_id);

    $this->load->view('orders/sub_form_product', $this->data);
  }

  public function updateStatus()
  {
    $order_id = $this->input->post('order_id');
    $seller_id = $this->input->post('temp_sellerID');
    $order_statusID = $this->input->post('order_status_id');

    $this->db->where('order_id',$order_id);
    $this->db->where('seller_id',$seller_id);
    $this->db->update('ci_order_status',array('order_status'=>$this->input->post('status')));

    $trans=array(
      'order_status_id'=>$order_statusID,
      'transaction_status'=>$this->input->post('status'),
      'transaction_record'=>date("Y-m-d H:i:s"),
      'next_transaction'=>null
    );
    $this->db->insert('ci_transaction',$trans);
    if($this->input->post('status')==4){
      $release=array(
        'order_id'=>$order_id,
        'seller_id'=>$seller_id
      );
      $this->db->insert('ci_release_payment',$release);

      $status="Completed";
    }else{
      $status="Cancel";
    }
    //audit
    if($this->data['user_profile']['user_group']!=2){
      $data_log=array(
        'ip_address'=>$ip = $this->input->ip_address(),
        'user_id'=>$this->data['user_profile']['id'],
        'username'=>$this->data['user_profile']['username'],
        'time_record'=>date("Y-m-d H:i:s"),
        'description'=>'Update order status to "'.$status.'" [Order ID:'.$order_id.']',
      );
      $this->db->insert('ci_audit_log',$data_log);
    }

    redirect('orders/detail/'.$order_id.'/'.$seller_id,'refresh');
  }

  public function receive_by_cust($order_id,$order_status_id,$seller_id)
  {
    $this->db->where('order_id',$order_id);
    $this->db->where('seller_id',$seller_id);
    $this->db->update('ci_order_status',array('order_status'=>4));

    $trans=array(
      'order_status_id'=>$order_status_id,
      'transaction_status'=>4,
      'transaction_record'=>date("Y-m-d H:i:s"),
      'next_transaction'=>null
    );
    $this->db->insert('ci_transaction',$trans);

    $release=array(
      'order_id'=>$order_id,
      'seller_id'=>$seller_id
    );
    $this->db->insert('ci_release_payment',$release);

    redirect('customer/view_order/'.$order_id,'refresh');
  }


  function download($file){
    $this->load->helper('download');
    // $pth    =   file_get_contents(base_url()."lampiran_belian");
    // $nme    =   $file;
    // force_download($nme, $pth);
    $pth    =   'payment_receipt/'.$file;
    // $nme    =   $file;
     force_download($pth, NULL);
  }

  public function store_siriproduct()
  {
    //$this->order_model->verify_payment($payment_id,$order_id);
    $arr_siri=$this->order_model->get_all_siri();

    $siri_item = $this->input->post('siri_item[]');
    $no_siri = $this->input->post('no_siri[]');

    $found=0;
    $new_nosiri=[];

    if(!empty($arr_siri)):
    for($i=0;$i<count($arr_siri);$i++){
      for($j=0;$j<count($siri_item);$j++){
        if($arr_siri[$i]['product_id']==$siri_item[$j])
        {
          if($arr_siri[$i]['serial_no']==strtoupper($no_siri[$j]))
          {
            $found=1;
            $new_nosiri[]=$no_siri[$j];

          }
        }
      }
    }
  endif;


    //$this->data['no_siri']=$no_siri;
    if($found==1)
    {
      $order_id = $this->input->post('order_id');
      $seller_id = $this->input->post('post_seller');
      //echo "<script type='text/javascript'> alert('$found')</script>";
        //echo "<script type='text/javascript'> alert('Redundent Siri No. :".json_encode($new_nosiri)."')</script>";
        $this->detail($order_id,$seller_id,$no_siri,$new_nosiri);
        //redirect('orders/detail/'.$order_id, 'refresh');
    }else
    {
        $order_id = $this->input->post('order_id');
        $found=0;

        for($k=0;$k<count($siri_item);$k++)
        {
            for($m=$k+1;$m<count($siri_item);$m++)
            {
              if($siri_item[$k]==$siri_item[$m])
              {
                  if($no_siri[$k]==$no_siri[$m] && $no_siri[$k]!='')
                  {
                    $found=1;
                    $arr_same[]=array('product_id' =>$siri_item[$m],'serial_no' =>$no_siri[$m] );
                  }
              }
            }
          }

        if($found==1)
        {
          //echo "<script type='text/javascript'> alert('No. Siri bertindih :".json_encode($arr_same)."')</script>";
          //redirect('orders/detail/'.$order_id, 'refresh');
          $order_id = $this->input->post('order_id');
          $seller_id = $this->input->post('post_seller');
          $this->detail($order_id,$seller_id,$no_siri,$arr_same);
          //$this->template->load('layouts/admin','orders/order_detail', $this->data);
        }else
        {
          //$payment_id = $this->input->post('payment_id');
          $order_id = $this->input->post('order_id');
          $seller_id = $this->input->post('post_seller');
          $this->order_model->store_siriproduct();
          //audit
          if($this->data['user_profile']['user_group']!=2){
            $data_log=array(
              'ip_address'=>$ip = $this->input->ip_address(),
              'user_id'=>$this->data['user_profile']['id'],
              'username'=>$this->data['user_profile']['username'],
              'time_record'=>date("Y-m-d H:i:s"),
              'description'=>'Update order status to "Processing" [Order ID:'.$order_id.']',
            );
            $this->db->insert('ci_audit_log',$data_log);
          }
          redirect('orders/detail/'.$order_id.'/'.$seller_id, 'refresh');
        }
    }
  }

  public function store_tracking()
  {
    $buyer_email=$this->input->post('buyer_email');
    $tracking_no=$this->input->post('no_tracking');
    $courier=$this->input->post('courier');
    $order_id=$this->input->post('order_id');
    $seller_id=$this->input->post('seller_id');

    $track=array(
      'order_id'=>$order_id,
      'seller_id'=>$seller_id,
      'tracking_code'=>$tracking_no,
      'courier_name'=>strtoupper($courier),
    );
    $this->db->insert('ci_tracking',$track);

    $this->db->where('order_id',$order_id);
    $this->db->where('seller_id',$seller_id);
    $this->db->update('ci_order_status',array('order_status'=>3));

    $due=$this->admin_model->get_setting_day();
    if($due['day_to_complete'] > 0)
    {
      $next_due = '+ '.$due['day_to_complete'].' days';
    }else{
      $next_due = '+ 7 days'; //default
    }
    //add history
      $trans=array(
        'order_status_id'=>$this->input->post('order_status_id'),
        'transaction_status'=>3,
        'transaction_record'=>date("Y-m-d H:i:s"),
        'next_transaction'=>date("Y-m-d H:i:s",strtotime($next_due))
      );
      $this->db->insert('ci_transaction',$trans);

      //send email to buyer
      $buyer_email=array(
        'subject'=>"Penghantaran barang pesanan",
        'message'=>"Assalamualaikum dan Selamat Sejahtera.<br><br>Pesanan anda bernombor #".$order_id." dalam proses penghantaran. No Tracking anda adalah ".$tracking_no." dari kurier ".$courier,
        'buyer_email'=>$buyer_email,
      );
      $this->to_buyer($buyer_email);

    //audit
    if($this->data['user_profile']['user_group']!=2){
      $data_log=array(
        'ip_address'=>$ip = $this->input->ip_address(),
        'user_id'=>$this->data['user_profile']['id'],
        'username'=>$this->data['user_profile']['username'],
        'time_record'=>date("Y-m-d H:i:s"),
        'description'=>'Update order status to "Shipping out" [Order ID:'.$order_id.']',
      );
      $this->db->insert('ci_audit_log',$data_log);
    }

    redirect('orders/detail/'.$order_id.'/'.$seller_id, 'refresh');
  }

  public function print_order($order_id,$seller_id){
    //audit
    if($this->data['user_profile']['user_group']!=2){
      $data_log=array(
        'ip_address'=>$ip = $this->input->ip_address(),
        'user_id'=>$this->data['user_profile']['id'],
        'username'=>$this->data['user_profile']['username'],
        'time_record'=>date("Y-m-d H:i:s"),
        'description'=>'Print order details [Order ID:'.$order_id.']',
      );
      $this->db->insert('ci_audit_log',$data_log);
    }

    $this->data['seller_id'] = $seller_id;
    $this->data['seller'] = $this->user_model->get_detail_seller($seller_id);
    $this->data['orders'] = $this->order_model->get_order(null,$order_id);
    $this->data['items'] = $this->order_model->get_items($order_id);
    $this->data['pid']=$this->order_model->get_productID_2($order_id);
    $this->data['order_status'] = $this->order_model->get_order_status($order_id,$seller_id);
    //if($this->data['orders']['status']>=2){
    $this->data['siri']=$this->order_model->get_siri($order_id,$seller_id);
    $this->data['track']=$this->order_model->get_track($order_id,$seller_id);

    //$this->template->load('layouts/admin','prints/print_order', $this->data);
    $html=$this->load->view('prints/print_order', $this->data, TRUE);

    // $this->load->library('m_pdf');
    $pdfFilePath = 'Order Receipt [ '.$this->data['seller']['shop_name'].' ] - '.$order_id.'.pdf';

    // define('_MPDF_TTFONTDATAPATH','uploads');
    // $this->m_pdf->showImageErrors = true;
    // $this->m_pdf->pdf->setFooter('Printed Date '.date('d/m/Y').' - Coffee-Fastlane.com');
    // $this->m_pdf->pdf->AddPage('L');
    // $this->m_pdf->pdf->WriteHTML($html);
    // $this->m_pdf->pdf->Output($pdfFilePath, 'I');
    $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
    $mpdf->setFooter('Printed Date '.date('d/m/Y').' - Coffee-Fastlane.com');
    $mpdf->AddPage('L');
    $mpdf->WriteHTML($html);
    $mpdf->Output($pdfFilePath, 'I');
  }

  public function notify_seller()
  {
      $seller_id=$_GET['seller_id'];
      $order_id=$_GET['order_id'];
      $data=json_decode($_GET['json'],true);
      $this->data['seller']=$this->user_model->get_detail_seller($seller_id);
      //notify seller
      $subject1 = "Pesanan baru berjaya dibuat";
      $message1 = '<html><body>';
      $message1 .= "Assalamualaikum dan Selamat Sejahtera.<br><br>Pesanan bernombor #".$order_id." telah berjaya dibuat dan pembayaran telah disahkan. Anda diminta untuk menguruskan proses penghantaran kepada pelanggan sebelum ".date('d-m-y',strtotime('+ 3 days'))." .";
      $message1 .= "<br><br>Terima kasih.";
      $message1 .= "</body></html>";

      $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
      $this->email->to($this->data['seller']['email']);
      $this->email->subject($subject1);
      $this->email->message($message1);
      if($this->email->send())
      {

        $subject = "Pesanan berjaya dihantar kepada penjual";
        $message = '<html><body>';
        $message .= "Assalamualaikum dan Selamat Sejahtera.<br><br>Pesanan anda bernombor #".$order_id." telah berjaya dihantar kepada penjual. Sila tunggu untuk proses selanjutnya.";
        $message .= "<br><br>Terima kasih.";
        $message .= "</body></html>";

        $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
        $this->email->to($data['email']);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->send();

        $this->db->where('order_id',$order_id);
        $this->db->where('seller_id',$seller_id);
        $this->db->update('ci_order_status',array('order_status'=>1));

        //add history
          $trans=array(
            'order_status_id'=>$data['order_status_id'],
            'transaction_status'=>1,
            'transaction_record'=>date("Y-m-d H:i:s"),
            'next_transaction'=>date("Y-m-d H:i:s",strtotime('+ 3 days'))
          );
          $this->db->insert('ci_transaction',$trans);

          //audit
          if($this->data['user_profile']['user_group']!=2){
            $data_log=array(
              'ip_address'=>$ip = $this->input->ip_address(),
              'user_id'=>$this->data['user_profile']['id'],
              'username'=>$this->data['user_profile']['username'],
              'time_record'=>date("Y-m-d H:i:s"),
              'description'=>'Notify seller [Order ID:'.$order_id.']',
            );
            $this->db->insert('ci_audit_log',$data_log);
          }

        $m="Notify seller successful.";
        //  echo "<script type='text/javascript'>alert('$m');</script>";
        // redirect('orders/detail/'.$order_id.'/'.$seller_id, 'refresh');
        echo "<script type='text/javascript'>alert('$m');window.location.href = '" . base_url() . "orders/detail/".$order_id."/".$seller_id."';</script>";
      }else{
        $m="Notify seller unsuccessful.";
        //  echo "<script type='text/javascript'>alert('$m');</script>";
        // redirect('orders/detail/'.$order_id.'/'.$seller_id, 'refresh');
        echo "<script type='text/javascript'>alert('$m');window.location.href = '" . base_url() . "orders/detail/".$order_id."/".$seller_id."';</script>";
      }
  }

//   public function cancel_order()
//   {
//     $order_id=$_GET['order_id'];
//     $data=json_decode($_GET['json'],true);

//     $this->db->where('order_id',$order_id);
//     $this->db->update('ci_order_status',array('order_status'=>6));

//     $this->db->where('order_id',$order_id);
//     $this->db->update('ci_orders',array('expired'=>1));

//     foreach ($data as $key) {
//       // $this->db->where('order_status_id',$key['order_status_id']);
//       // $this->db->update('ci_transaction',array('transaction_status'=>6));
//       $trans=array(
//         'order_status_id'=>$key['order_status_id'],
//         'transaction_status'=>6,
//         'transaction_record'=>date("Y-m-d H:i:s"),
//         'next_transaction'=>date("Y-m-d H:i:s")
//       );
//       $this->db->insert('ci_transaction',$trans);
//     }
//     // $m=$_GET['json'];
//     // echo "<script type='text/javascript'>alert('$m');</script>";

//     redirect('customer/view_order/'.$order_id,'refresh');
//   }

public function cancel_order_by_cust()
  {
    $order_id = $this->input->post('order_id');
    $res = $this->input->post('seller_id');
    $exp = explode(',',$res);
    $seller_id=$exp[0];
    $order_status_id=$exp[1];

    $check_order = $this->order_model->get_items($order_id);

    foreach ($check_order as $key) {
        if($key['order_id']==$order_id && $seller_id==$key['seller_id'])
        {
          $this->db->where('order_id',$order_id);
          $this->db->where('product_id',$key['product_id']);
          $this->db->delete('ci_inventory');
        }
    }

    $cancel_data=array(
      'order_status'=>6,
      'cancelled_desc'=>strtoupper($this->input->post('reasons'))
    );

    $this->db->where('order_id',$order_id);
    $this->db->where('seller_id',$seller_id);
    $this->db->update('ci_order_status',$cancel_data);

    $trans=array(
      'order_status_id'=>$order_status_id,
      'transaction_status'=>6,
      'transaction_record'=>date("Y-m-d H:i:s"),
      'next_transaction'=>date("Y-m-d H:i:s")
    );
    $this->db->insert('ci_transaction',$trans);

    redirect('customer/view_order/'.$order_id,'refresh');
  }

  public function store_cancel_order()
  {
    $order_id = $this->input->post('order_id');
    $order_status_id = $this->input->post('order_status_id');
    $seller_id = $this->input->post('seller_id');

    $check_order = $this->order_model->get_items($order_id);

    foreach ($check_order as $key) {
        if($key['order_id']==$order_id && $seller_id==$key['seller_id'])
        {
          $this->db->where('order_id',$order_id);
          $this->db->where('product_id',$key['product_id']);
          $this->db->delete('ci_inventory');
        }
    }

    $cancel_data=array(
      'order_status'=>6,
      'cancelled_desc'=>strtoupper($this->input->post('reasons'))
    );

    $this->db->where('order_id',$order_id);
    $this->db->where('seller_id',$seller_id);
    $this->db->update('ci_order_status',$cancel_data);

    $trans=array(
      'order_status_id'=>$order_status_id,
      'transaction_status'=>6,
      'transaction_record'=>date("Y-m-d H:i:s"),
      'next_transaction'=>date("Y-m-d H:i:s")
    );
    $this->db->insert('ci_transaction',$trans);

    redirect('orders/detail/'.$order_id.'/'.$seller_id,'refresh');
  }

  // Send email section ------------------------------------------------------->
  public function to_seller($data_email=array())
      {
        //   include_once("sms_gateway/sms_config.php");
        //   try {
        //         // Send a message using the primary device.
        //         $msg_sms = "RM 0.00 COFFEE-FASTLANE : \n";
        //         $msg = sendSingleMessage($data_email['seller_phone'], $msg_sms.$data_email['message'],1232);

        //         // Send a message using the Device ID 1.
        //         // $msg = sendSingleMessage("+911234567890", "This is a test of single message.", 1);
        //         // print_r($msg);
        //         if($msg['status']!='Sent')
        //         {
        //           $subject = $data_email['subject'];
        //           $message_seller = "<html><body>";
        //           $message_seller .= $data_email['message'];
        //           $message_seller .= "<br><br>Terima kasih.";
        //           $message_seller .= "</body></html>";

        //           $this->load->library('email');
        //           $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
        //           $this->email->to($data_email['seller_email']);
        //           $this->email->subject($subject);
        //           $this->email->message($message_seller);
        //           $this->email->send();
        //         }
        //     } catch (Exception $e) {
                // echo $e->getMessage();
                $subject = $data_email['subject'];
                $message_seller = "<html><body>";
                $message_seller .= $data_email['message'];
                $message_seller .= "<br><br>Terima kasih.";
                $message_seller .= "</body></html>";

                $this->load->library('email');
                $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
                $this->email->to($data_email['seller_email']);
                $this->email->subject($subject);
                $this->email->message($message_seller);
                $this->email->send();
            //}
      }

      public function to_buyer($data_email=array())
          {
              $subject = $data_email['subject'];
              $message_buyer = "<html><body>";
              $message_buyer .= $data_email['message'];
              $message_buyer .= "<br><br>Terima kasih.";
              $message_buyer .= "</body></html>";

              $this->load->library('email');
              $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
              $this->email->to($data_email['buyer_email']);
              $this->email->subject($subject);
              $this->email->message($message_buyer);
              $this->email->send();
            // if( $this->email->send()) {
            //     return true;
            //  }else {
            //    return false;
            //  }
          }

          public function to_admin($data_email=array())
              {
                  $subject = $data_email['subject'];
                  $message_admin = "<html><body>";
                  $message_admin .= $data_email['message'];
                  $message_admin .= "<br><br>Terima kasih.";
                  $message_admin .= "</body></html>";

                  $this->load->library('email');

                  if(!empty($list_email)){
                    foreach ($list_email as $key) {
                      $mail[]=$key['email'];
                    }
                  }

                  $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
                  // $this->email->to('ddfastlane@gmail.com');
                  $this->email->to($mail);
                  $this->email->subject($subject);
                  $this->email->message($message_admin);
                  $this->email->send();
                // if( $this->email->send()) {
                //     return true;
                //  }else {
                //    return false;
                //  }
              }

}
