<?php 
	require_once __DIR__ . '\Conexao.php';
	$limite_km 		= $_REQUEST['limite_km'];
	$lat 			= $_REQUEST['latitude'];
	$long 			= $_REQUEST['longitude'];


	$query = Conexao::getInstance()->prepare("
		SELECT local.id,
			(6371 * acos(
			 cos( radians(:latitude) )
			 * cos( radians( local.lat ) )
			 * cos( radians( local.long ) - radians(:longitude) )
			 + sin( radians(:latitude) )
			 * sin( radians( local.lat ) ) 
			 )
			) AS distancia,
            local.nome,
            local.descricao,
            categoria.titulo as categoria,
            local.lat,
            local.long,
			(SELECT avg(nota) FROM avaliacao WHERE local = local.id) as avaliacao
		FROM local,categoria
        WHERE categoria.id = local.categoria
		HAVING distancia < 35
		ORDER BY distancia ASC
		LIMIT 30;
	");
	$query->bindValue("limite_km"	,	$limite_km		, PDO::PARAM_INT);
	$query->bindValue("latitude"	,	$lat							);
	$query->bindValue("longitude"	,	$long  							);
	$query->execute();

	echo json_encode($query->fetchAll());


