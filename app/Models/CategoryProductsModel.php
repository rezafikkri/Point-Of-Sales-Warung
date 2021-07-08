<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryProductsModel extends Model
{
    protected $table = 'category_products';
    protected $primaryKey = 'category_product_id';
    protected $allowedFields = [
        'category_product_id',
        'category_product_name',
        'created_at',
        'updated_at'
    ];
    protected $useAutoIncrement = false;

    public function getCategoryProducts(): array
    {
        return $this->orderBy('updated_at', 'DESC')->findAll();
    }

    public function findCategoryProduct(string $category_product_id): ? array
    {
        return $this->select('nama_kategori_produk')->getWhere(['kategori_produk_id' => $category_product_id])->getRowArray();
    }

    public function removeCategoryProduct(string $category_product_id): int
    {
        try {
            $this->delete($category_product_id);
            return $this->db->affectedRows();
        } catch(\ErrorException $e) {
            return 0;
        }
    }

    public function getCategoryProductsForFormSelect(): array
    {
        return $this->select('kategori_produk_id, nama_kategori_produk')->get()->getResultArray();
    }
}
