<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catalog extends CI_Controller{

  public function __construct()
  {
    parent::__construct();
    $this->load->model(array('product_model','admin_model','order_model'));

    if ($this->ion_auth->logged_in()) {
      $user = $this->ion_auth->user()->row();
      $this->data['user_profile'] = $this->user_model->get_profile($user->id);
      $this->data['shop'] = $this->user_model->get_seller($user->id);
    }

    //$this->data['metal'] = $this->product_model->get_gs_price();
    $this->data['products'] = $this->product_model->get_all_product();
    $this->data['logo'] = $this->admin_model->get_logo();
    $this->data['pm'] = $this->product_model->get_productCategory();
    $this->data['footer'] = $this->admin_model->get_footer();
    $this->data['count_order'] = $this->order_model->count_order();
    $this->output->enable_profiler(false);
  }

  public function index()
  {
    $this->products();
  }

  public function products()
  {
    $this->data['title'] = 'Products List';
    if(!$this->ion_auth->logged_in()){
      redirect('user/login','refresh');
    }
    if ($this->data['user_profile']['user_group'] == 1 || $this->data['user_profile']['user_group']==0) {

      $this->data['products'] = $this->product_model->get_all_product();
    }
    if ($this->data['user_profile']['user_group'] == 2) {

      $this->data['products'] = $this->product_model->get_all_product(null,$this->data['shop']['seller_id']);
    }
    $this->template->load('layouts/admin','product/table', $this->data);
  }

  // Register new product
  public function new_product()
  {
    if(!$this->ion_auth->logged_in()){
      redirect('user/login','refresh');
    }

    $this->data['title'] = 'Add Products';

    $this->form_validation->set_rules('product_name', 'Product Name', 'required');
    $this->form_validation->set_rules('item_code', 'Product Code', 'required');
    $this->form_validation->set_rules('product_code', 'Kod Tag Siri', '');

    $this->form_validation->set_rules('category_id', 'Product Type', 'required');
    $this->form_validation->set_rules('weight', 'Weight (g)', 'required');
    $this->form_validation->set_rules('shipping', 'Shipping Cost', 'required');
    $this->form_validation->set_rules('tax', 'Tax', 'required');
    $this->form_validation->set_rules('add_cost', 'Sell Price', 'required');
    $this->form_validation->set_rules('description', 'Description', 'trim');
    $this->form_validation->set_rules('product_price', 'Modal Price', 'required');
    $this->form_validation->set_rules('size', 'Size', 'required');

    // Muat naik imej
    $config['upload_path'] = 'images';
    $config['allowed_types']  = 'jpg|png|jpeg';
    $config['max_width']  =  1500;
    $config['max_height']  =  1500;
    $config['encrypt_name']  =  TRUE;
    $config['remove_spaces']  =  TRUE;
    $config['file_ext_tolower']  =  TRUE;
    $config['overwrite']  =  FALSE;


    $this->load->library('upload', $config);

    if ($this->form_validation->run() == TRUE && $this->upload->do_upload('userfile')) {

      $upload_data = $this->upload->data();
      $image_file = $upload_data['raw_name'] . $upload_data['file_ext'];

      // Simpan imej dalam database
      $imageData = array(
        'file_name' => $image_file
      );

      $this->db->insert('ci_images',$imageData);

      $image_id = $this->db->insert_id(); // redirect('product/upload_success', 'refresh');

      $this->load->helper('string');
      $clean_product = preg_replace("/[^a-zA-Z0-9\s\p{P}]/", "", $this->input->post('product_name'));

      $product_slug = url_title(date('YmdHms').'-'.$clean_product, 'dash', true);

      if($this->data['user_profile']['user_group']==0 || $this->data['user_profile']['user_group']==1){
        $product = array(
          'product_name' => strtoupper($this->input->post('product_name')),
          'item_code' => strtoupper($this->input->post('item_code')),
          'product_code' => null,
          'seller_id' => $this->input->post('seller_id'),
          'product_price' => empty($this->input->post('product_price')) ? 0 : $this->input->post('product_price'),
          'created_date'=>date("Y-m-d H:i:s")
        );
      }else{
        $product = array(
          'product_name' => strtoupper($this->input->post('product_name')),
          'item_code' => strtoupper($this->input->post('item_code')),
          'product_code' => null,
          'seller_id' => $this->data['shop']['seller_id'],
          'product_price' => empty($this->input->post('product_price')) ? 0 : $this->input->post('product_price'),
          'created_date'=>date("Y-m-d H:i:s")
        );
      }


      $this->product_model->store_product($product,$image_id);

      if ($this->upload->display_errors()) {
        $this->data['error_image'] = $this->upload->display_errors();
      }

    } else {

      if ($this->upload->display_errors()) {
        $this->data['error_image'] = $this->upload->display_errors();
      }

      // Dropdown input
      $this->data['seller'] = $this->user_model->dropdown_seller();
      $this->data['cType'] = $this->product_model->dropdown_category();
      $this->data['gold_list'] = $this->product_model->dropdown_category(1);
      $this->data['silver_list'] = $this->product_model->dropdown_category(2);

      $this->data['product_name'] = array(
        'name'=>'product_name',
        'id'=>'product_name',
        'autocomplete' => 'off',
        'class' =>'form-control uppercase maxlength-handler',
        'maxlength' => '120',
        'value' => $this->form_validation->set_value('product_name')
      );

      $this->data['description'] = array(
        'name'=>'description',
        'id'=>'description',
        'autocomplete' => 'off',
        'class' =>'form-control',
        'value' => $this->form_validation->set_value('description')
      );

      $this->data['item_code'] = array(
        'name'=>'item_code',
        'id'=>'item_code',
        'class' =>'form-control uppercase',
        'autocomplete' => 'off',
        'value' => $this->form_validation->set_value('item_code')
      );

      $this->data['add_cost'] = array(
        'name'=>'add_cost',
        'id'=>'add_cost',
        'class' =>'form-control',
        'autocomplete' => 'off',
        'value' => $this->form_validation->set_value('add_cost')
      );

      $this->data['product_price'] = array(
        'name'=>'product_price',
        'id'=>'product_price',
        'class' =>'form-control',
        'autocomplete' => 'off',
        'value' => $this->form_validation->set_value('product_price',set_value('product_price'))
      );

      $this->data['weight'] = array(
        'name'=>'weight',
        'autocomplete' => 'off',
        'id'=>'weight',
        'class' =>'form-control',
        'value' => $this->form_validation->set_value('weight',set_value('weight'))
      );

      $this->data['size'] = array(
        'name'=>'size',
        'autocomplete' => 'off',
        'id'=>'size',
        'class' =>'form-control',
        'value' => $this->form_validation->set_value('size',set_value('size'))
      );

      $this->data['shipping'] = array(
        'name'=>'shipping',
        'autocomplete' => 'off',
        'id'=>'shipping',
        'class' =>'form-control',
        'value' => $this->form_validation->set_value('shipping',set_value('shipping'))
      );

      $this->data['tax'] = array(
        'name'=>'tax',
        'autocomplete' => 'off',
        'id'=>'tax',
        'class' =>'form-control',
        'value' => $this->form_validation->set_value('tax',set_value('tax'))
      );

      if (set_value('metal_id')) {
        $id = set_value('metal_id');
        $this->data['metal'] = $this->product_model->get_metal($id);
      }


      $this->template->load('layouts/admin', 'product/register_new_item', $this->data);
    }

  }

  // View product
  public function product_detail($id)
  {
      $p=$this->uri->segment(4);
      $this->data['p_detail'] = $this->product_model->product_view($id);
      $this->data['imej'] = $this->product_model->get_other_image($id);
    if(!$this->ion_auth->logged_in()){
      $this->template->load('layouts/main', 'product/product_detail', $this->data);
    }
    if($this->ion_auth->logged_in()){
      if(($this->data['user_profile']['user_group']==1 || $this->data['user_profile']['user_group']==0 || $this->data['shop']['seller_id']!='') && $p==''){
        $this->data['title'] = 'Product Details';
        $this->data['product'] = $this->product_model->owner_product($id);

        $this->template->load('layouts/admin', 'product/detail', $this->data);
      }else if($p==1){
        $this->template->load('layouts/main', 'product/product_detail', $this->data);
      }
    }
  }

  // Get mettal from id from ajax request
  public function get_form_for_metal()
  {

    $id = $this->input->post('category_id');

    $this->data['metal'] = $this->product_model->get_metal($id);

    $this->data['product_price'] = array(
      'name'=>'product_price',
      'id'=>'product_price',
      'class' =>'form-control',
      'autocomplete' => 'off',
      'value' => $this->form_validation->set_value('product_price')
    );

    $this->data['add_cost'] = array(
      'name'=>'add_cost',
      'id'=>'add_cost',
      'class' =>'form-control',
      'autocomplete' => 'off',
      'value' => $this->form_validation->set_value('add_cost')
    );

    $this->data['weight'] = array(
      'name'=>'weight',
      'autocomplete' => 'off',
      'id'=>'weight',
      'class' =>'form-control',
      'value' => $this->form_validation->set_value('weight')
    );



    $this->data['size'] = array(
      'name'=>'size',
      'autocomplete' => 'off',
      'id'=>'size',
      'class' =>'form-control',
      'value' => $this->form_validation->set_value('size')
    );

    $this->data['shipping'] = array(
      'name'=>'shipping',
      'autocomplete' => 'off',
      'id'=>'shipping',
      'class' =>'form-control',
      'value' => $this->form_validation->set_value('shipping')
    );

    $this->data['tax'] = array(
      'name'=>'tax',
      'autocomplete' => 'off',
      'id'=>'tax',
      'class' =>'form-control',
      'value' => $this->form_validation->set_value('tax')
    );


    $this->data['description'] = array(
      'name'=>'description',
      'autocomplete' => 'off',
      'id'=>'description',
      'class' =>'form-control',
      'value' => $this->form_validation->set_value('description')
    );

    $this->load->view('product/ajax_metal_form', $this->data);
  }

  public function get_product_vendor()
  {

    $id = $this->input->post('seller_id');
    $this->data['sproduct'] = $this->product_model->drop_down_products($id);
    $this->data['seller_id'] = $id;
    $this->load->view('product/ajax_seller_product', $this->data);
  }

  // View product
  public function product_edit($id)
  {

    $this->data['title'] = 'Edit Product';
    if(!$this->ion_auth->logged_in()){
      redirect('user/login','refresh');
    }
    if ($this->data['user_profile']['user_group']==1 || $this->data['user_profile']['user_group']==0 || $this->data['shop']['seller_id']!='') {
      $this->data['seller'] = $this->user_model->dropdown_seller();
      $this->data['product'] = $this->product_model->owner_product($id);
      $product = $this->product_model->owner_product($id);
    }

    $this->data['category'] = $this->product_model->get_category();
    //$this->data['vendor'] = $this->product_model->list_vendor();

    $this->data['product_name'] = array(
      'name'=>'product_name',
      'id'=>'product_name',
      'autocomplete' => 'off',
      'class' =>'form-control uppercase maxlength-handler',
      'maxlength' => '120',
      'value' => $this->form_validation->set_value('product_name',$product['product_name'])
    );

    $this->data['description'] = array(
      'name'=>'description',
      'id'=>'description',
      'autocomplete' => 'off',
      'class' =>'form-control',
      'value' => $this->form_validation->set_value('description',$product['description'])
    );

    $this->data['item_code'] = array(
      'name'=>'item_code',
      'id'=>'item_code',
      'class' =>'form-control uppercase',
      'autocomplete' => 'off',
      'value' => $this->form_validation->set_value('item_code',$product['item_code'])
    );

    $this->data['product_code'] = array(
      'name'=>'product_code',
      'id'=>'product_code',
      'class' =>'form-control uppercase',
      'autocomplete' => 'off',
      'value' => $this->form_validation->set_value('product_code',$product['product_code'])
    );

    $this->data['add_cost'] = array(
      'name'=>'add_cost',
      'id'=>'add_cost',
      'class' =>'form-control',
      'autocomplete' => 'off',
      'value' => $this->form_validation->set_value('add_cost',$product['add_cost'])
    );

    $this->data['product_price'] = array(
      'name'=>'product_price',
      'id'=>'product_price',
      'class' =>'form-control',
      'autocomplete' => 'off',
      'value' => $this->form_validation->set_value('product_price',$product['product_price'])
    );

    $this->data['shipping'] = array(
      'name'=>'shipping',
      'autocomplete' => 'off',
      'id'=>'shipping',
      'class' =>'form-control',
      'value' => $this->form_validation->set_value('shipping',$product['shipping'])
    );

    $this->data['tax'] = array(
      'name'=>'tax',
      'autocomplete' => 'off',
      'id'=>'tax',
      'class' =>'form-control',
      'value' => $this->form_validation->set_value('tax',$product['tax']*100)
    );

    if ($product['weight']) {
      $this->data['weight'] = array(
        'name'=>'weight',
        'autocomplete' => 'off',
        'id'=>'weight',
        'class' =>'form-control',
        'value' => $this->form_validation->set_value('weight',number_format($product['weight'],3))
      );
    }

    $this->data['size'] = array(
      'name'=>'size',
      'autocomplete' => 'off',
      'id'=>'size',
      'class' =>'form-control',
      'value' => $this->form_validation->set_value('size',$product['size'])
    );

    $this->template->load('layouts/admin', 'product/edit', $this->data);
  }


  public function store_product_update()
  {
    $product_id = $this->input->post('product_id');
    $this->form_validation->set_rules('product_name', 'Product Name', 'required');
    $this->form_validation->set_rules('item_code', 'Product Code', 'required');
    $this->form_validation->set_rules('product_code', 'Kod Tag Siri', '');
    $this->form_validation->set_rules('category_id', 'Product Type', 'required');
    $this->form_validation->set_rules('weight', 'Weight (g)', 'required');
    $this->form_validation->set_rules('shipping', 'Shipping Cost', 'required');
    $this->form_validation->set_rules('tax', 'Tax', 'required');
    $this->form_validation->set_rules('add_cost', 'Sell Price', 'required');
    $this->form_validation->set_rules('description', 'Description', 'trim');
    $this->form_validation->set_rules('product_price', 'Modal Price', 'required');
    $this->form_validation->set_rules('size', 'Size', 'required');


    // Muat naik imej
    $config['upload_path'] = 'images';
    $config['allowed_types']  = 'jpg|png|jpeg';
    $config['max_width']  =  1500;
    $config['max_height']  =  1500;
    $config['encrypt_name']  =  TRUE;
    $config['remove_spaces']  =  TRUE;
    $config['file_ext_tolower']  =  TRUE;
    $config['overwrite']  =  FALSE;

    $this->load->library('upload', $config);

    if ($this->form_validation->run() == TRUE || $this->upload->do_upload('userfile')) {

      if ($this->upload->do_upload('userfile')) {
        $upload_data = $this->upload->data();
        $image_file = $upload_data['raw_name'] . $upload_data['file_ext'];

        // Simpan imej dalam database
        $imageData = array(
          'file_name' => $image_file
        );

        $this->db->insert('ci_images',$imageData);

        $image_id = $this->db->insert_id(); // redirect('product/upload_success', 'refresh');
      } else {
        $image_id = '';
      }

      if($this->data['user_profile']['user_group']==0 || $this->data['user_profile']['user_group']==1){
        $product = array(
          'product_name' => strtoupper($this->input->post('product_name')),
          'item_code' => strtoupper($this->input->post('item_code')),
          'product_code' => null,
          'seller_id' => $this->input->post('seller_id'),
          'product_price' => empty($this->input->post('product_price')) ? 0 : $this->input->post('product_price'),
          'created_date'=>date("Y-m-d H:i:s")
        );
      }else{
        $product = array(
          'product_name' => strtoupper($this->input->post('product_name')),
          'item_code' => strtoupper($this->input->post('item_code')),
          'product_code' => null,
          'seller_id' => $this->data['shop']['seller_id'],
          'product_price' => empty($this->input->post('product_price')) ? 0 : $this->input->post('product_price'),
          'created_date'=>date("Y-m-d H:i:s")
        );
      }

      $this->product_model->store_update_product($product,$image_id);

      if ($this->upload->display_errors()) {
        $this->data['error_image'] = $this->upload->display_errors();
      }

      //redirect('catalog/product_detail/'.$product_id, 'refresh');

    } else {
      $this->product_edit($product_id);
    }

  }

  // Delete product form list
  public function product_delete($product_id)
  {
    $this->db->where('product_id', $product_id);
    $this->db->delete(array('ci_inventory','ci_products'));

    redirect('catalog', 'refresh');
  }

  public function add_stock()
  {

    $this->data['title'] = 'Manage Stock';
    if(!$this->ion_auth->logged_in()){
      redirect('user/login','refresh');
    }
    $id=$this->data['shop']['seller_id'];
    $this->data['seller'] = $this->user_model->dropdown_seller();
    $this->data['list_products'] = $this->product_model->drop_down_products($id);
    //$this->data['vendor'] = $this->product_model->dropdown_vendor();

    $this->template->load('layouts/admin', 'product/add_stock', $this->data);
  }

  public function store_inventory()
  {
    $data = array(
      'owner_id' => $this->data['shop']['seller_id'],
      'product_id' => $this->input->post('product_id'),
      'qty' => $this->input->post('qty'),
      'owner_type' => 'company',
      'created_date'=>date("Y-m-d H:i:s")
    );
    $this->db->insert('ci_inventory', $data);
    $inventory_id=$this->db->insert_id();
    //audit
    $data_log=array(
      'ip_address'=>$ip = $this->input->ip_address(),
      'user_id'=>$this->data['user_profile']['id'],
      'username'=>$this->data['user_profile']['username'],
      'time_record'=>date("Y-m-d H:i:s"),
      'description'=>'Manage stock product [Inventory ID:'.$inventory_id.']',
    );
    $this->db->insert('ci_audit_log',$data_log);

    redirect('catalog/products', 'refresh');
  }

  public function vendor(){
    $this->data['title'] = 'Vendors List';
    if(!$this->ion_auth->logged_in()){
      redirect('user/login','refresh');
    }
    $this->data['vendor'] = $this->product_model->list_vendor2();

    $this->template->load('layouts/admin', 'vendor/list_vendor', $this->data);
  }

  public function store_vendor(){

    // Muat naik imej
    $config['upload_path'] = 'logo_vendor';
    $config['allowed_types']  = 'jpg|png|jpeg';
    $config['max_width']  =  1500;
    $config['max_height']  =  1500;
    $config['encrypt_name']  =  TRUE;
    $config['remove_spaces']  =  TRUE;
    $config['file_ext_tolower']  =  TRUE;
    $config['overwrite']  =  FALSE;

    $this->load->library('upload', $config);

    if ($this->upload->do_upload('vendor_logo')!='') {

      $upload_data = $this->upload->data();
      $image_file = $upload_data['raw_name'] . $upload_data['file_ext'];

      // Simpan imej dalam database
      $vendor=array(
        'vendor_name'=>strtoupper($this->input->post('vendor_name')),
        'vendor_phone'=>$this->input->post('vendor_phone'),
        'vendor_email'=>$this->input->post('vendor_email'),
        'vendor_address'=>strtoupper($this->input->post('vendor_address')),
        'vendor_logo' => $image_file,
        'vendor_status'=>1,
      );

      $this->db->insert('ci_vendor',$vendor);
      $vendor_id=$this->db->insert_id();
      //audit
      $data_log=array(
        'ip_address'=>$ip = $this->input->ip_address(),
        'user_id'=>$this->data['user_profile']['id'],
        'username'=>$this->data['user_profile']['username'],
        'time_record'=>date("Y-m-d H:i:s"),
        'description'=>'Add new vendor [Vendor ID:'.$vendor_id.']',
      );
      $this->db->insert('ci_audit_log',$data_log);
    }else{
      $vendor=array(
        'vendor_name'=>strtoupper($this->input->post('vendor_name')),
        'vendor_phone'=>$this->input->post('vendor_phone'),
        'vendor_email'=>$this->input->post('vendor_email'),
        'vendor_address'=>strtoupper($this->input->post('vendor_address')),
        'vendor_status'=>1,
      );

      $this->db->insert('ci_vendor',$vendor);
      $vendor_id=$this->db->insert_id();
      //audit
      $data_log=array(
        'ip_address'=>$ip = $this->input->ip_address(),
        'user_id'=>$this->data['user_profile']['id'],
        'username'=>$this->data['user_profile']['username'],
        'time_record'=>date("Y-m-d H:i:s"),
        'description'=>'Add new vendor [Vendor ID:'.$vendor_id.']',
      );
      $this->db->insert('ci_audit_log',$data_log);
    }

    redirect('catalog/vendor','refresh');
  }

  public function get_vendor(){
    $vendor_id=$this->input->post('vendor_id');
    $this->data['vendor'] = $this->product_model->list_vendor2($vendor_id);
    $this->load->view('vendor/vendor_detail', $this->data);
  }

  public function upd_vendor(){

    // Muat naik imej
    $config['upload_path'] = 'logo_vendor';
    $config['allowed_types']  = 'jpg|png|jpeg';
    $config['max_width']  =  1500;
    $config['max_height']  =  1500;
    $config['encrypt_name']  =  TRUE;
    $config['remove_spaces']  =  TRUE;
    $config['file_ext_tolower']  =  TRUE;
    $config['overwrite']  =  FALSE;

    $this->load->library('upload', $config);

    if ($this->upload->do_upload('vendor_logo')!='') {
      if($this->input->post('temp_logo')!=''){
        unlink("logo_vendor/".$this->input->post('temp_logo'));
      }

      $upload_data = $this->upload->data();
      $image_file = $upload_data['raw_name'] . $upload_data['file_ext'];

      $vendor=array(
        'vendor_name'=>strtoupper($this->input->post('vendor_name')),
        'vendor_phone'=>$this->input->post('vendor_phone'),
        'vendor_email'=>$this->input->post('vendor_email'),
        'vendor_address'=>strtoupper($this->input->post('vendor_address')),
        'vendor_logo' => $image_file,
        'vendor_status'=>$this->input->post('vendor_status'),
      );
      $this->db->where('vendor_id',$this->input->post('vendor_id'));
      $this->db->update('ci_vendor',$vendor);
    }else{
      $vendor=array(
        'vendor_name'=>strtoupper($this->input->post('vendor_name')),
        'vendor_phone'=>$this->input->post('vendor_phone'),
        'vendor_email'=>$this->input->post('vendor_email'),
        'vendor_address'=>strtoupper($this->input->post('vendor_address')),
        'vendor_status'=>$this->input->post('vendor_status'),
      );

      $this->db->where('vendor_id',$this->input->post('vendor_id'));
      $this->db->update('ci_vendor',$vendor);
    }

    //audit
    $data_log=array(
      'ip_address'=>$ip = $this->input->ip_address(),
      'user_id'=>$this->data['user_profile']['id'],
      'username'=>$this->data['user_profile']['username'],
      'time_record'=>date("Y-m-d H:i:s"),
      'description'=>'Update vendor [Vendor ID:'.$this->input->post('vendor_id').']',
    );
    $this->db->insert('ci_audit_log',$data_log);

    redirect('catalog/vendor','refresh');
  }

  public function store_other_image(){
    $product_id=$this->input->post('product_id');
    // Muat naik imej
    $config['upload_path'] = 'images';
    $config['allowed_types']  = 'jpg|png|jpeg';
    $config['max_width']  =  1500;
    $config['max_height']  =  1500;
    $config['encrypt_name']  =  TRUE;
    $config['remove_spaces']  =  TRUE;
    $config['file_ext_tolower']  =  TRUE;
    $config['overwrite']  =  FALSE;

    $this->load->library('upload', $config);

    if ($this->upload->do_upload('imej_tambahan')!='') {

      $upload_data = $this->upload->data();
      $image_file = $upload_data['raw_name'] . $upload_data['file_ext'];

      $otherImage=array(
        'image_add_file'=>$image_file,
        'product_id' => $product_id,
      );
      $this->db->insert('ci_image_addition',$otherImage);
      $image_add_id=$this->db->insert_id();

      //audit
      $data_log=array(
        'ip_address'=>$ip = $this->input->ip_address(),
        'user_id'=>$this->data['user_profile']['id'],
        'username'=>$this->data['user_profile']['username'],
        'time_record'=>date("Y-m-d H:i:s"),
        'description'=>'Add addition product image [Product ID:'.$product_id.',Image Addition ID:'.$image_add_id.']',
      );
      $this->db->insert('ci_audit_log',$data_log);
    }

    redirect('catalog/product_detail/'.$product_id,'refresh');
  }

  public function del_other_image($id,$file,$pID){
    $this->db->where('image_add_id',$id);
    $this->db->delete('ci_image_addition');

      unlink("images/".$file);
      //audit
      $data_log=array(
        'ip_address'=>$ip = $this->input->ip_address(),
        'user_id'=>$this->data['user_profile']['id'],
        'username'=>$this->data['user_profile']['username'],
        'time_record'=>date("Y-m-d H:i:s"),
        'description'=>'Delete addition product image [Product ID:'.$pID.',Image Addition ID:'.$id.']',
      );
      $this->db->insert('ci_audit_log',$data_log);

    redirect('catalog/product_detail/'.$pID,'refresh');
  }

  public function category(){
    $this->data['title'] = 'Products Category';
    if(!$this->ion_auth->logged_in()){
      redirect('user/login','refresh');
    }
    $this->data['category'] = $this->product_model->get_productCategory();
    $this->template->load('layouts/admin', 'product/list_category', $this->data);
  }

  public function get_category(){
    $cat_id=$this->input->post('category_id');
    $this->data['cType'] = $this->product_model->get_productCategory($cat_id);
    $this->load->view('product/category_detail', $this->data);
  }

  public function store_category(){
      $category=array(
        // 'quality'=>$this->input->post('quality'),
        // 'karat'=>$this->input->post('karat'),
        'category_type'=>strtoupper($this->input->post('category_type')),
      );

      $this->db->insert('ci_category',$category);
      $category_id=$this->db->insert_id();
      //audit
      $data_log=array(
        'ip_address'=>$ip = $this->input->ip_address(),
        'user_id'=>$this->data['user_profile']['id'],
        'username'=>$this->data['user_profile']['username'],
        'time_record'=>date("Y-m-d H:i:s"),
        'description'=>'Add new category product [Category ID:'.$category_id.']',
      );
      $this->db->insert('ci_audit_log',$data_log);

    redirect('catalog/category','refresh');
  }

  public function upd_category(){
      $category=array(
        // 'quality'=>$this->input->post('quality'),
        // 'karat'=>$this->input->post('karat'),
        'category_type'=>strtoupper($this->input->post('category_type')),
      );
      $this->db->where('category_id',$this->input->post('category_id'));
      $this->db->update('ci_category',$category);
      //audit
      $data_log=array(
        'ip_address'=>$ip = $this->input->ip_address(),
        'user_id'=>$this->data['user_profile']['id'],
        'username'=>$this->data['user_profile']['username'],
        'time_record'=>date("Y-m-d H:i:s"),
        'description'=>'Update category product [Category ID:'.$this->input->post('category_id').']',
      );
      $this->db->insert('ci_audit_log',$data_log);

    redirect('catalog/category','refresh');
  }

}
