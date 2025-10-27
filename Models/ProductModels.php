<?php

	class ProductModel {
		// Ruta del Archivo 
		private $dataPath = __DIR__ .'/../data/products.json';
		
		/***
		Funcion Para leer el archivo json y retornarlo como un array php
		***/
		
		private function readData(){
			if(!file_exists($this -> dataPath)){
				return [];
			}
			$json = file_get_contents($this-> dataPath);
			
			// Para retornar el json decodificado como array asociativo 
			return json_decode($json, true); 
		}
		
		/***
		Obtiene Todos los productos
		***/
		
		public function getAll(){
			return $this->readData();
		}
		
		/***
		Obtener un producto en especifico por su id
		***/
		
		public function getById($id){
			$products = readData();
			foreach($products as $product){
				if($product['id']== $id){
				 return $product;
				}
			}
			return null; // en caso de no encontrar el producto
		}
	
	}
?>