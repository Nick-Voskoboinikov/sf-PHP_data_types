<?

function getPartsFromFullname($strSNP){
$arrSNP=array_combine(['surname','name','patronymic'], explode(' ',$strSNP));
return $arrSNP;
}

function getFullnameFromParts($strSurname='Иванов',$strName='Иван',$strPatronymic='Иванович'){
$strSNP=$strSurname.' '.$strName.' '.$strPatronymic;
return $strSNP;
}

function getShortName($strSNP='Иванов Иван Иванович'){
$strShortName=getPartsFromFullname($strSNP)['name'] .' '. mb_substr(getPartsFromFullname($strSNP)['surname'], 0, 1) . '.';  //  'Иван И.'
return $strShortName;
}

function getGenderFromName($strSNP='Иванов Иван Иванович'){
$strAggregateGenderAttr=0; 
$arrSNP=getPartsFromFullname($strSNP);

// female gender checks
if (mb_substr($arrSNP['patronymic'], -3) == 'вна'){
$strAggregateGenderAttr--;
}
if (mb_substr($arrSNP['name'], -1) == 'а'){
$strAggregateGenderAttr--;
}
if (mb_substr($arrSNP['surname'], -2) == 'ва'){
$strAggregateGenderAttr--;
}

// male gender checks
if (mb_substr($arrSNP['patronymic'], -2) == 'ич'){
$strAggregateGenderAttr++;
}
if ((mb_substr($arrSNP['name'], -1) == 'й') || (mb_substr($arrSNP['name'], -1) == 'н')){
$strAggregateGenderAttr++;
}
if (mb_substr($arrSNP['surname'], -1) == 'в'){
$strAggregateGenderAttr++;
}

return $strAggregateGenderAttr <=> 0; // -1 - female, 1 - male, 0 - undefined
}

function getGenderDescription($arrPersons){
$arrMales = array_filter($arrPersons, function($arrPersons) {
    return getGenderFromName($arrPersons['fullname']) == 1;
});
$strMalePercentage=round( (count($arrMales) / (count($arrPersons)/100)), 2);
$arrFemales = array_filter($arrPersons, function($arrPersons) {
    return getGenderFromName($arrPersons['fullname']) == -1;
});
$strFemalePercentage=round( (count($arrFemales) / (count($arrPersons)/100)), 2);
$arrUndefined = array_filter($arrPersons, function($arrPersons) {
    return getGenderFromName($arrPersons['fullname']) == 0;
});
$strUndefinedPercentage=round( (count($arrUndefined ) / (count($arrPersons)/100)), 2);

return 'Гендерный состав аудитории:'.PHP_EOL.'---------------------------'.PHP_EOL.'Мужчины - '.$strMalePercentage.'%'.PHP_EOL.'Женщины - '.$strFemalePercentage.'%'.PHP_EOL.'Не удалось определить - '.$strUndefinedPercentage.'%';
}

function getPerfectPartner($strSurname,$strName,$strPatronymic,$arrPersons){
$strSNP=mb_convert_case(getFullnameFromParts($strSurname,$strName,$strPatronymic), MB_CASE_TITLE);
$strGender=getGenderFromName($strSNP) * (-1);
do {
  $arrRandomlyFetchedPerson=$arrPersons[rand(1,count($arrPersons)-1)]; // fetch a random person from the array
  $strMatchingSNP=$arrRandomlyFetchedPerson['fullname'];
} while ( getGenderFromName( $strMatchingSNP ) !== $strGender); // check for opposite sex
$strMatchValue=(rand(5000,10000))/100;
return getShortName($strSNP).' + '.getShortName($strMatchingSNP).' ='.PHP_EOL.'&#9825; Идеально на '.$strMatchValue.'% &#9825;';
}



echo '<!DOCTYPE html>
<html lang="ru">
	<head>
		<title>ФИО и случайные пары</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
                <style>
body {
  margin: 0;
  padding: 0;
  background: #ccc;
}

.box {
  position: relative;
  margin: 40px auto;
  width: 80vw;
  min-height: 87vh;
  background: #fff;
  border-radius: 2px;
}

.box::before,
.box::after {
  content: "";
  position: absolute;
  bottom: 10px;
  width: 40%;
  height: 10px;
  box-shadow: 0 5px 14px rgba(0, 0, 0, 0.7);
  z-index: -1;
  transition: all 0.3s ease-in-out;
}

.box::before {
  left: 15px;
  transform: skew(-5deg) rotate(-5deg);
}

.box::after {
  right: 15px;
  transform: skew(5deg) rotate(5deg);
}

.box:hover::before,
.box:hover::after {
  box-shadow: 0 2px 14px rgba(0, 0, 0, 0.4);
}

.box:hover::before {
  left: 5px;
}

.box:hover::after {
  right: 5px;
}

form {
  padding: 15px;
}

fieldset > textarea.result {
right: 15px;
}

fieldset > textarea.result:not(:empty){
border: 3px solid red;
}

</style>
	</head>
	<body>
            <div class="box">
             <form action="'. htmlspecialchars($_SERVER['PHP_SELF']) .'" method="POST" target="_self" >
<fieldset><legend>Разбиение ФИО</legend>
<input type="text" name="fullname" placeholder="Фамилия Имя Отчество" value="Иванов Иван Иванович" onfocus="this.select();" required> &nbsp; <input type="submit" value="Функция &quot;getPartsFromFullname&quot;" name="getPartsFromFullname" />
<textarea placeholder="Result" cols="40" rows="8" class="result" >';
if ( !empty ( $_POST['getPartsFromFullname'] )) {
var_dump(getPartsFromFullname($_POST['fullname']));
}
echo '</textarea>
</fieldset>
             </form><hr>
             <form action="'. htmlspecialchars($_SERVER['PHP_SELF']) .'" method="POST" target="_self" >
<fieldset><legend>Объединение ФИО</legend>
<input type="text" name="surname" placeholder="Фамилия" value="Иванов" onfocus="this.select();" required><br>
<input type="text" name="name" placeholder="Имя" value="Иван" onfocus="this.select();" required><br>
<input type="text" name="patronymic" placeholder="Отчество" value="Иванович" onfocus="this.select();" required><br>
 &nbsp; <input type="submit" value="Функция &quot;getFullnameFromParts&quot;" name="getFullnameFromParts" />
<textarea placeholder="Result" cols="40" rows="3" class="result" >';
if ( !empty ( $_POST['getFullnameFromParts'] )) {
var_dump(getFullnameFromParts($_POST['surname'],$_POST['name'],$_POST['patronymic']));
}
echo '</textarea>
</fieldset>
             </form><hr>
             <form action="'. htmlspecialchars($_SERVER['PHP_SELF']) .'" method="POST" target="_self" >
<fieldset><legend>Сокращение ФИО</legend>
<input type="text" name="fullname" placeholder="Фамилия Имя Отчество" value="Иванов Иван Иванович" onfocus="this.select();" required> &nbsp; <input type="submit" value="Функция &quot;getShortName&quot;" name="getShortName" />
<textarea placeholder="Result" cols="40" rows="3" class="result" >';
if ( !empty ( $_POST['getShortName'] )) {
var_dump(getShortName($_POST['fullname']));
}
echo '</textarea>
</fieldset>
             </form><hr>
             <form action="'. htmlspecialchars($_SERVER['PHP_SELF']) .'" method="POST" target="_self" >
<fieldset><legend>Функция определения пола по ФИО</legend>
<input type="text" name="fullname" placeholder="Фамилия Имя Отчество" value="Иванов Иван Иванович" onfocus="this.select();" required> &nbsp; <input type="submit" value="Функция &quot;getGenderFromName&quot;" name="getGenderFromName" />
<textarea placeholder="Result" cols="40" rows="3" class="result" >';
if ( !empty ( $_POST['getGenderFromName'] )) {
var_dump(getGenderFromName($_POST['fullname']));
}
echo '</textarea>
</fieldset>
             </form><hr>
             <form action="'. htmlspecialchars($_SERVER['PHP_SELF']) .'" method="POST" target="_self" >
<fieldset><legend>Определение возрастно-полового состава</legend>
<textarea placeholder="$example_persons_array" cols="40" rows="3" name="persons_array" >
[
{"fullname": "Иванов Иван Иванович",
"job": "tester"},
{"fullname": "Степанова Наталья Степановна",
"job": "frontend-developer"},
{"fullname": "Пащенко Владимир Александрович",
"job": "analyst"},
{"fullname": "Громов Александр Иванович",
"job": "fullstack-developer"},
{"fullname": "Славин Семён Сергеевич",
"job": "analyst"},
{"fullname": "Цой Владимир Антонович",
"job": "frontend-developer"},
{"fullname": "Быстрая Юлия Сергеевна",
"job": "PR-manager"},
{"fullname": "Шматко Антонина Сергеевна",
"job": "HR-manager"},
{"fullname": "аль-Хорезми Мухаммад ибн-Муса",
"job": "analyst"},
{"fullname": "Бардо Жаклин Фёдоровна",
"job": "android-developer"},
{"fullname": "Шварцнегер Арнольд Густавович",
"job": "babysitter"}
]
</textarea>
<input type="submit" value="Функция &quot;getGenderDescription&quot;" name="getGenderDescription" />
<textarea placeholder="Result" cols="40" rows="5" class="result" >';
if ( !empty ( $_POST['getGenderDescription'] )) {
echo getGenderDescription(json_decode($_POST['persons_array'], true));
}
echo '</textarea>
</fieldset>
             </form><hr>
             <form action="'. htmlspecialchars($_SERVER['PHP_SELF']) .'" method="POST" target="_self" >
<fieldset><legend>Идеальный подбор пары</legend>
<input type="text" name="surname" placeholder="Фамилия" value="ИВАНОВ" onfocus="this.select();" required><br>
<input type="text" name="name" placeholder="Имя" value="ИВАН" onfocus="this.select();" required><br>
<input type="text" name="patronymic" placeholder="Отчество" value="ИВАНОВИЧ" onfocus="this.select();" required><br>
<textarea placeholder="$example_persons_array" cols="40" rows="3" name="persons_array" >
[
{"fullname": "Иванов Иван Иванович",
"job": "tester"},
{"fullname": "Степанова Наталья Степановна",
"job": "frontend-developer"},
{"fullname": "Пащенко Владимир Александрович",
"job": "analyst"},
{"fullname": "Громов Александр Иванович",
"job": "fullstack-developer"},
{"fullname": "Славин Семён Сергеевич",
"job": "analyst"},
{"fullname": "Цой Владимир Антонович",
"job": "frontend-developer"},
{"fullname": "Быстрая Юлия Сергеевна",
"job": "PR-manager"},
{"fullname": "Шматко Антонина Сергеевна",
"job": "HR-manager"},
{"fullname": "аль-Хорезми Мухаммад ибн-Муса",
"job": "analyst"},
{"fullname": "Бардо Жаклин Фёдоровна",
"job": "android-developer"},
{"fullname": "Шварцнегер Арнольд Густавович",
"job": "babysitter"}
]
</textarea>
<input type="submit" value="Функция &quot;getPerfectPartner&quot;" name="getPerfectPartner" />
<textarea placeholder="Result" cols="40" rows="2" class="result" >';
if ( !empty ( $_POST['getPerfectPartner'] )) {
echo getPerfectPartner($_POST['surname'],$_POST['name'],$_POST['patronymic'],json_decode($_POST['persons_array'], true));
}
echo '</textarea>
</fieldset>
             </form>
</div>
	</body>
</html>';
