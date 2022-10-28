<?php

use function PHPSTORM_META\type;

$SRID = '4326';
if (isset($_POST['function'])) {
    $paPDO = initDB();
    if(isset($_POST['point']))
        $paPoint = $_POST['point'];
    if(isset($_POST['distance']))
        $distance = $_POST['distance']*= 0.0005;
    $function = $_POST['function'];

    $aResult = "null";
    if ($function == 'getSingle')
        $aResult = getSingle($paPDO, $paPoint, $distance);
    else if($function == 'listAll')
        $aResult = listAll($paPDO, $paPoint);
    else if($function == 'add')
        $aResult = add($paPDO, $_POST['item']);
    else if($function == 'edit')
        $aResult = edit($paPDO, $_POST['item']);
    else if($function == 'delete')
        $aResult = delete($paPDO, $paPoint,$distance);
    else if($function == 'isInHN')
        $aResult = isInHN($paPDO, $paPoint);
    echo $aResult;

    closeDB($paPDO);
}
function initDB()
{
    // Kết nối CSDL
    $paPDO = new PDO('pgsql:host=localhost;dbname=InternetCafe;port=5432', 'postgres', '1');
    return $paPDO;
}
function query($paPDO, $paSQLStr)
{
    try {
        // Khai báo exception
        $paPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Sử đụng Prepare 
        $stmt = $paPDO->prepare($paSQLStr);
        // Thực thi câu truy vấn
        $stmt->execute();

        // Khai báo fetch kiểu mảng kết hợp
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        // Lấy danh sách kết quả
        $paResult = $stmt->fetchAll();
        return $paResult;
    } catch (PDOException $e) {
        echo "Thất bại, Lỗi: " . $e->getMessage();
        return null;
    }
}

function closeDB($paPDO)
{
    // Ngắt kết nối
    $paPDO = null;
}

function isInHN($pdo, $point)
{
    global $SRID;
    $mySQLStr = "select st_contains(geom, ST_GeometryFromText('$point', $SRID)) as res from bg";
    $result = query($pdo, $mySQLStr);
    if ($result != null) {
        return $result[0]['res'];
    }
    return false;
}

function getSingle($pdo,$point, $distance)
{
    global $SRID;
    $mySQLStr = "select * from loc where st_contains(ST_Buffer(geom, $distance), ST_GeometryFromText('$point', $SRID));";
    $result = query($pdo, $mySQLStr);
    if ($result != null) {
        return json_encode($result[0]);
    }
    return 'null';
}

function listAll($pdo,$point){
    global $SRID;
    $mySQLStr = "select * from loc order by ST_Distance(ST_GeometryFromText('$point', $SRID), geom);";
    $result = query($pdo, $mySQLStr);
    if ($result != null) {
        return json_encode($result);
    }
    return 'null';
}

function add($pdo, $item){
    $mySQLStr = "INSERT INTO loc(\"name\",addr,device_num,min_price,max_price,opening_hour,phone_num,url,geom) 
                 VALUES ('".$item['name']."', '".$item['addr']."', ".$item['device_num'].", ".$item['min_price'].", ".$item['max_price'].", '".$item['opening_hour']."', '".$item['phone_num']."', '".$item['url']."', '".$item['geom']."');";
    $result = query($pdo, $mySQLStr);
    if ($result) {
        return true;
    }
    return false;
}

function edit($pdo, $item){
    $mySQLStr = "UPDATE loc SET name = '".$item['name']."', addr = '".$item['addr']."', device_num = ".$item['device_num'].", min_price = ".$item['min_price'].", max_price = ".$item['max_price'].", phone_num = '".$item['phone_num']."', url = '".$item['url']."', opening_hour = '".$item['opening_hour']."'
                 WHERE id = ".$item['id'].";";
    $result = query($pdo, $mySQLStr);
    if ($result) {
        return true;
    }
    return false;
}

function delete($pdo, $point, $distance){
    $item = getSingle($pdo,$point,$distance);
    if($item = 'null')
        return false;
    $item = json_decode($item);
    $mySQLStr = "DELETE FROM loc WHERE id = ".$item['id'].";";
    $result = query($pdo, $mySQLStr);
    if ($result) {
        return true;
    }
    return false;
}