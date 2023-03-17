<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
ini_set('max_execution_time', '300');
ini_set('memory_limit', '-1');
set_time_limit(300);
date_default_timezone_set('Asia/Jakarta');

class Data extends Base_controller
{

    public $db2;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('format_helper');
    }

    public function getProduct()
    {
        $method = $this->input->server('REQUEST_METHOD');
        switch ($method) {
            case 'GET':

                try {
                    $this->db->select('a.*')
                        ->from('ms_product a')
                        ->where('a.deleted', 0);

                    $data = $this->db->get()->result();

                    if (empty($data)) {
                        $this->rest->send_error(400, 'data is empty');
                        exit;
                    }
                    $this->rest->send($data);
                } catch (\Throwable $th) {
                    $this->rest->send_error(400, 'data is not found');
                }

                break;

            default:
                $this->rest->send_error(401, 'Method is invalid');
                break;
        }
    }

    public function addProduct()
    {
        $method = $this->input->server('REQUEST_METHOD');
        switch ($method) {
            case 'POST':
                $params = json_decode(file_get_contents('php://input'), true);

                if (empty($params['product_name'])) {
                    $this->rest->send_error(400, 'Nama Produk dibutuhkan');
                    exit;
                }
                if (empty($params['product_description'])) {
                    $this->rest->send_error(400, 'Deskripsi Produk dibutuhkan');
                    exit;
                }

                try {
                    $getNameProduct = $this->db->get_where('ms_product', ['deleted' => '0', 'product_name' => $params['product_name']])->row();
                    if (empty($getNameProduct)) {
                        $datas = [
                            'product_name'          => $params['product_name'],
                            'product_description'   => $params['product_description']
                        ];
                        $this->db->insert('ms_product', $datas);
                        $this->rest->send('Success Insert Data', 200);
                    } else {
                        $this->rest->send_error(400, 'Nama produk tidak boleh sama');
                    }
                } catch (\Throwable $th) {
                    $this->rest->send_error(400, 'data is not found');
                }

                break;
        }
    }

    public function editProduct($product_id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        switch ($method) {
            case 'PUT':
                $params = json_decode(file_get_contents('php://input'), true);

                if (empty($params['product_name'])) {
                    $this->rest->send_error(400, 'Nama Produk dibutuhkan');
                    exit;
                }
                if (empty($params['product_description'])) {
                    $this->rest->send_error(400, 'Deskripsi Produk dibutuhkan');
                    exit;
                }

                if (empty($product_id)) {
                    $this->rest->send_error(400, 'Produk ID dibutuhkan');
                    exit;
                }

                try {
                    $getProduct = $this->db->get_where('ms_product', ['product_id' => $product_id])->row();
                    if (empty($getProduct)) {
                        $this->rest->send_error(400, 'Produk dengan ID tersebut tidak ditemukan');
                    } else {
                        $getNameProduct = $this->db->get_where('ms_product', ['deleted' => '0', 'product_name' => $params['product_name']])->row();
                        if (empty($getNameProduct)) {
                            $datas = [
                                'product_name'          => $params['product_name'],
                                'product_description'   => $params['product_description']
                            ];
                            $this->db->where('product_id', $product_id);
                            $this->db->update('ms_product', $datas);
                            $this->rest->send('Success Edit Data', 200);
                        } else {
                            $this->rest->send_error(400, 'Nama produk tidak boleh sama');
                        }
                    }
                } catch (\Throwable $th) {
                    $this->rest->send_error(400, 'data is not found');
                }

                break;
        }
    }

    public function softdeleteProduct($product_id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        switch ($method) {
            case 'DELETE':
                if (empty($product_id)) {
                    $this->rest->send_error(400, 'Produk ID dibutuhkan');
                    exit;
                }

                try {
                    $getProduct = $this->db->get_where('ms_product', ['product_id' => $product_id])->row();
                    if (empty($getProduct)) {
                        $this->rest->send_error(400, 'Produk dengan ID tersebut tidak ditemukan');
                    } else {
                        $datas = [
                            'deleted'          => 1
                        ];
                        $this->db->where('product_id', $product_id);
                        $this->db->update('ms_product', $datas);
                        $this->rest->send('Success Delete Data', 200);
                    }
                } catch (\Throwable $th) {
                    $this->rest->send_error(400, 'data is not found');
                }

                break;
        }
    }

    public function harddeleteProduct($product_id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        switch ($method) {
            case 'DELETE':
                if (empty($product_id)) {
                    $this->rest->send_error(400, 'Produk ID dibutuhkan');
                    exit;
                }
                try {
                    $getProduct = $this->db->get_where('ms_product', ['product_id' => $product_id])->row();
                    if (empty($getProduct)) {
                        $this->rest->send_error(400, 'Produk dengan ID tersebut tidak ditemukan');
                    } else {
                        $this->db->where('product_id', $product_id);
                        $this->db->delete('ms_product');
                        $this->rest->send('Success Delete Data', 200);
                    }
                } catch (\Throwable $th) {
                    $this->rest->send_error(400, 'data is not found');
                }

                break;
        }
    }
}
