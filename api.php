<?php

use function PHPSTORM_META\type;

$SRID = '4326';
if (isset($_POST['function'])) {
    $paPDO = initDB();

    $paPoint = $_POST['point'];
    $function = $_POST['function'];

    $aResult = "null";
    if ($function == 'getSingle')
        $aResult = getSingle($paPDO, $paPoint, $_POST['distance']);
    else if($function == 'listAll')
        $aResult = listAll($paPDO, $paPoint);
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
    $distance *= 0.0005;
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
