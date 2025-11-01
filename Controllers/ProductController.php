<?php
// 1. Incluir el modelo para usarlo
require_once __DIR__ . '/../Models/ProductModels.php';

class ProductController
{
    private $productModel;

    // Constructor para inicializar el modelo
    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    /***
        Maneja la peticion GET /products
     ***/

    public function index()
    {
        $products = $this->productModel->getAll();
        echo json_encode($products);
    }

    /***
        Maneja la peticion GET /products/{id}
     ***/

    public function show($id)
    {
        // llama al modelo para obtener el producto por id
        $product = $this->productModel->getById($id);
        if ($product) {
            // Retorna el producto en formato JSON OK
            echo json_encode($product);
        } else {
            // Retorna un mensaje de error si no se encuentra el producto
            header("HTTP/1.0 404 Not Found");
            echo json_encode(['message' => 'Producto no encontrado']);
        }
    }
}
