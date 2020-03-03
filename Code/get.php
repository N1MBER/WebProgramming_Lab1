
<!DOCTYPE html>
<html>
<head>
    <title>Result</title>
    <meta charset="utf-8">
    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <style>

       .answer{
           font-size: 10pt;
           font-family: Arial;
           border-color: burlywood;
           border-style: double;
           border: 5px;
       }
       /*table{*/
       /*    font-size: 10pt;*/
       /*    font-weight: bold;*/
       /*    margin-left: 100px;*/
       /*    font-family: Arial ;*/
       /*    border: darkgray;*/
       /*    border-radius: 100px;*/
       /*    background-color: antiquewhite;*/
       /*    border-style: double;*/
       /*    border-color: darkgoldenrod;*/
       /*}*/
       tr,td {
           font-size: 10pt;
           font-weight: bold;
           margin-left: 100px;
           font-family: Arial ;
           border: darkgray;
           /*border-radius: 100px;*/
           background-color: antiquewhite;
           border-style: double;
           border-color: darkgoldenrod;
       }
       .time{
           font-weight: bold;
       }
       .time_area{
           text-align: left;
           float: none;
           margin-left: 5%;
       }
    </style>
</head>
<body>
<?php
date_default_timezone_set("Europe/Moscow");
$request_time=date("H:i:s", time());
$start_time = microtime(true);
echo "<table class = 'time_area'>Текущее время: <span class = 'time' id='time'></span><br>";
echo "Время запроса: <span class = 'time'>".$request_time."</span><br>";
echo "Время вычисления(с): <span class = 'time' id='timedone'></span></table><br>";

if(session_id()===""){
    session_start();
}
$arrayR=array();
$flagX = false;
$flagR = false;
$flagY = true;

if (isset($_GET['radiox'])) {
    $X = $_GET['radiox'];
    $flagX = true;
    if($X <=-2 and $X >= 2)
        $flagX =false;
}

echo "<table class='answer'>";

if(!$flagX){
    echo "Выберите координату X<br>";
}
if(!isset($_GET['texty'])){
    echo "Введите Y<br>";
    $flagY=false;
    $Y=null;
} else {
    $Y = $_GET['texty'].trim();
    if (!strcmp($Y, "")) {
        echo "Введите Y<br>";
        $flagY = false;
    } else {
        if (!is_numeric(str_replace(',', '.', $_GET['texty']))) {
            echo "Y должен быть числом<br>";
            $flagY = false;
        } else {
            if(substr($_GET['texty'], 0, 1) === '-'&&(float)str_replace(',', '.', $_GET['texty'])==0){
                $Y=0;
            } else {
                $Y = (float)str_replace(',', '.', $_GET['texty']);
                if (($Y <= -5) || ($Y >= 3)) {
                    echo "Y находится вне диапазона<br>";
                    $flagY = false;
                }
            }
        }
    }
}

for ($j = 1;$j<=5;$j++){
    if (isset($_GET['r'.$j])){
        array_push($arrayR,$_GET['r'.$j]);
        $flagR = true;
    }
}
foreach ($arrayR as $value){
    if ($value>3 or $value<1)
        $flagR = false;
}
if (!$flagR)
    echo "Введите R<br>";




echo "<br></table>";

if (!isset($_SESSION['points'])) {
    $_SESSION['points'] = array();
}

if($flagR&&$flagY&&$flagX){
    foreach ($arrayR as $valueR){
        $point = new Point($X, $Y, $valueR, $request_time);
        array_push($_SESSION['points'], $point);
    }
}

echo "<table  align='center' class='count' id='count'>
    <tr id = 'header'>
    <td><h5>Координата Х</h5></td>
    <td><h5>Координата Y</h5></td>
    <td><h5>Радиус R</h5></td>
    <td><h5>Поподание</h5></td>
    <td><h5>Время</h5></td>
    </tr>";
foreach (array_reverse($_SESSION['points']) as $point)
{
    echo "<tr>
    <td>$point->x</td>
    <td>$point->y</td>
    <td>$point->r</td>";
    echo $point->check()? "<td>Да</td>" : "<td>Нет</td>";
    echo "<td>$point->time</td>";
    echo "</tr>";
}

echo "</table>";

$time = (float)round( microtime(true) - $start_time,6);
if ($time==0){
    $time = "Менее 0.000001";
}

class Point{
    public $x;
    public $y;
    public $r;
    public $time;

    function __construct($x,$y,$r, $time)
    {
        $this->x=$x;
        $this->y=$y;
        $this->r=$r;
        $this->time=$time;
    }
    function check(){
        if ($this->x >= 0 && $this->y>=0)
            if ($this->x<=$this->r and $this->y<=$this->r)
                return true;
            else return false;
        if($this->x<=0&&$this->y>=0){
            return hypot($this->x,$this->y)<=$this->r;
        }
        if ($this->x<0 and $this->y<0) return false;
        if($this->x>=0&&$this->y<=0) {
            return (($this->y+$this->r/2)-$this->x*$this->r/2)>=0;
        }
        return false;
    }
}
?>
<script>
    function show()
    {
        $.ajax({
            url: "time.php",
            cache: false,
            success: function(html){
                $("#time").html(html);
            }
        });
    }

    $(document).ready(function(){
        show();
        setInterval('show()',1000);
    });

    document.getElementById('timedone').innerHTML = '<?php echo $time;?>'
</script>
</body>
</html>