<?php

  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Credentials: true");
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
  header("Content-Type: application/json; charset=utf-8");

  include "library/config.php";
  
  $postjson = json_decode(file_get_contents('php://input'), true);
  $today    = date('Y-m-d');


  if($postjson['aksi']=='addPagos'){

    $queryCk = mysqli_query($mysqli, "SELECT * from pagos where idorden = $postjson[idOrden]");

    $rowcount=mysqli_num_rows($queryCk);

    if ($rowcount > 0) {
      while($row = mysqli_fetch_array($queryCk)){
        $query_sub = mysqli_query($mysqli, "INSERT INTO detalle_pagos (idpago, monto, pagos_idpago, pagos_tipo_pago_idtipopago) 
        VALUES ($row[idpago], $postjson[monto], $row[idpago], $postjson[idTipoPago])");
      }
    }else{
      $query = mysqli_query($mysqli, "INSERT INTO pagos (idorden, idtipopago, idusuario, estatus, created, updated,
          tipo_pago_idtipopago, usuarios_idusuario, usuarios_empresas_idempresa, usuarios_roles_idrol, ordenes_idorden) 
          VALUES ($postjson[idOrden], $postjson[idTipoPago], $postjson[idusuario], 1, now(), now(), $postjson[idTipoPago],
          $postjson[idusuario], $postjson[idempresa], $postjson[idrol], $postjson[idOrden])");

      $qstring = "INSERT INTO pagos (idorden, idtipopago, idusuario, estatus, created, updated,
          tipo_pago_idtipopago, usuarios_idusuario, usuarios_empresas_idempresa, usuarios_roles_idrol, ordenes_idorden) 
          VALUES ($postjson[idOrden], $postjson[idTipoPago], $postjson[idusuario], 1, now(), now(), $postjson[idTipoPago],
          $postjson[idusuario], $postjson[idempresa], $postjson[idrol], $postjson[idOrden])";

      $idpago = mysqli_insert_id($mysqli);

      $query_sub = mysqli_query($mysqli, "INSERT INTO detalle_pagos (idpago, monto, pagos_idpago, pagos_tipo_pago_idtipopago) 
      VALUES ($idpago, $postjson[monto], $idpago, $postjson[idTipoPago])");
    }

    if($query_sub) $result = json_encode(array('success'=>true, 'result'=>'success'));
    else $result = json_encode(array('success'=>false, 'result'=>'error'));

  	echo $result;

  }

  elseif($postjson['aksi']=='doFilterOrden'){
  	$data = array();
  	$query = mysqli_query($mysqli, "SELECT * FROM ordenes WHERE identifier_order = '$postjson[idOrden]'");

  	while($row = mysqli_fetch_array($query)){

  		$data[] = array(
        'idorden' => $row['idorden'],
  			'identifier_order' => $row['identifier_order'],
  			'client' => $row['client'],
        'cost' => $row['cost']
  			// 'desc_customer' => $row['desc_customer'],
  			// 'created_at' => $row['created_at'],

  		);
  	}

  	if($query) $result = json_encode(array('success'=>true, 'result'=>$data));
  	else $result = json_encode(array('success'=>false));

  	echo $result;

  }

  // elseif($postjson['aksi']=='doFilterOrdenRemesa'){
  // 	$data = array();
  // 	$query = mysqli_query($mysqli, "SELECT * FROM ordenes WHERE identifier_order = '$postjson[idOrden]'");

  // 	while($row = mysqli_fetch_array($query)){
  //     if (count($row) > 0) {
  //       $queryR = mysqli_query($mysqli, "");
  //     }else{
  //       $result = json_encode(array('success'=>false)
  //     }
  // 		$data[] = array(
  //       'idorden' => $row['idorden'],
  // 			'identifier_order' => $row['identifier_order'],
  // 			'client' => $row['client']
  // 			// 'desc_customer' => $row['desc_customer'],
  // 			// 'created_at' => $row['created_at'],

  // 		);
  // 	}

  // 	if($query) $result = json_encode(array('success'=>true, 'result'=>$data));
  // 	else $result = json_encode(array('success'=>false));

  // 	echo $result;

  // }

  elseif($postjson['aksi']=='doRefreshTipoPago'){
  	$data = array();
  	$query = mysqli_query($mysqli, "SELECT * FROM tipo_pago where estatus = 1");

  	while($row = mysqli_fetch_array($query)){

  		$data[] = array(
        'idtipopago' => $row['idtipopago'],
  			'nombre_tipo_pago' => $row['nombre_tipo_pago']
  		);
  	}

  	if($query) $result = json_encode(array('success'=>true, 'result'=>$data));
  	else $result = json_encode(array('success'=>false));

  	echo $result;

  }

  elseif($postjson['aksi']=='doRefreshEmpresa'){
  	$data = array();
  	$query = mysqli_query($mysqli, "SELECT * FROM empresas where estatus = 1");

  	while($row = mysqli_fetch_array($query)){

  		$data[] = array(
        'idempresa' => $row['idempresa'],
  			'nombre_empresa' => $row['nombre_empresa']
  		);
  	}

  	if($query) $result = json_encode(array('success'=>true, 'result'=>$data));
  	else $result = json_encode(array('success'=>false));

  	echo $result;

  }
  
  elseif($postjson['aksi']=='doRefreshOrdenRemesa'){
  	$data = array();
  	$query = mysqli_query($mysqli, "SELECT b.idorden,b.identifier_order FROM pagos a
    INNER JOIN ordenes b ON a.idorden = b.idorden");

  	while($row = mysqli_fetch_array($query)){

  		$data[] = array(
        'idorden' => $row['idorden'],
  			'identifier_order' => $row['identifier_order']
  		);
  	}

  	if($query) $result = json_encode(array('success'=>true, 'result'=>$data));
  	else $result = json_encode(array('success'=>false));

  	echo $result;

  }
  elseif($postjson['aksi']=='doRefreshBanco'){
  	$data = array();
  	$query = mysqli_query($mysqli, "SELECT 
            a.idbanco,
            CONCAT(a.banco,' - ', b.cuenta) banco
          FROM banco a 
          INNER JOIN cuenta_banco b ON a.idbanco = b.idbanco
          WHERE a.estatus = 1 ORDER BY a.banco");

  	while($row = mysqli_fetch_array($query)){

  		$data[] = array(
        'idbanco' => $row['idbanco'],
  			'banco' => $row['banco']
  		);
  	}

    if($query) $result = json_encode(array('success'=>true, 'result'=>$data));
  	else $result = json_encode(array('success'=>false));

  	echo $result;
    
  }
  elseif($postjson['aksi']=='update'){
  	$query = mysqli_query($mysqli, "UPDATE master_customer SET 
  		name_customer='$postjson[name_customer]',
  		desc_customer='$postjson[desc_customer]' WHERE customer_id='$postjson[customer_id]'");

  	if($query) $result = json_encode(array('success'=>true, 'result'=>'success'));
  	else $result = json_encode(array('success'=>false, 'result'=>'error'));

  	echo $result;

  }

  elseif($postjson['aksi']=='delete'){
  	$query = mysqli_query($mysqli, "DELETE FROM master_customer WHERE customer_id='$postjson[customer_id]'");

  	if($query) $result = json_encode(array('success'=>true, 'result'=>'success'));
  	else $result = json_encode(array('success'=>false, 'result'=>'error'));

  	echo $result;

  }

  elseif($postjson['aksi']=="login"){
    $password = md5($postjson['password']);
    $query = mysqli_query($mysqli, "SELECT * FROM usuarios WHERE username='$postjson[username]' AND password='$password'");
    $check = mysqli_num_rows($query);

    if($check>0){
      $data = mysqli_fetch_array($query);
      $datauser = array(
        'user_id' => $data['idusuario'],
        'username' => $data['username'],
        'password' => $data['password'],
        'idrol' => $data['idrol'],
        'idempresa' => $data['idempresa']
      );

      if($data['estatus']==1){
        $result = json_encode(array('success'=>true, 'result'=>$datauser));
      }else{
        $result = json_encode(array('success'=>false, 'msg'=>'Cuenta Inactiva')); 
      }

    }else{
      $result = json_encode(array('success'=>false, 'msg'=>'El usuario no existe'));
    }

    echo $result;
  }

  elseif($postjson['aksi']=="register"){
    $password = md5($postjson['password']);
    $query = mysqli_query($mysqli, "INSERT INTO master_user SET
      username = '$postjson[username]',
      password = '$password',
      status   = 'y'
    ");

    if($query) $result = json_encode(array('success'=>true));
    else $result = json_encode(array('success'=>false, 'msg'=>'error, please try again'));

    echo $result;
  }


?>