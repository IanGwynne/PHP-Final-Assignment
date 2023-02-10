<?php
// http://localhost/HaworthIanCodingAsst/asstmain.php 
// Ian Haworth
require_once("asstInclude.php");
require_once("clsDeleteSunglassRecord.php");
$mysqlObj = CreateConnectionObject();

function DisplayMainForm()
{
  DisplayButton('f_CreateTable',"Create Table","button_create-table.png");
  DisplayButton("f_AddRecord","Add Record","button_add-record.png");
  DisplayButton("f_DeleteRecord","Delete Record","button_delete-record.png");
  DisplayButton("f_DisplayData","Display Data","button_display-data.png");
}

function CreateTableForm(&$mysqlObj,$TableName)
{
  if (($stmt = $mysqlObj->prepare("Drop table $TableName"))) 
	$stmt->execute();

  $BrandName = "BrandName varchar(10)";
	$DateManufactured = "DateManufactured date";
	$CameraMP = "CameraMP int";
	$Colour = "Colour varchar(15)";
	$stmt = $mysqlObj->prepare("Create Table $TableName($BrandName,
  $DateManufactured, $CameraMP, $Colour,primary key (BrandName))");
	if ($stmt == false) 
	{	
		echo "Prepare failed on query $SQLStatement";
		exit;
	}
	$CreateResult = $stmt->execute();
	if ($CreateResult) 
		echo "Table $TableName created.";
	else
		echo "Can't create table $TableName.";

	$stmt->close();
  echo "<div class=\"datapair\">"; 
  DisplayButton("f_Home","Home","button_home.png"); 
}

function addRecordForm(&$mysqlObj,$TableName)
{
  echo "<form action = ? method=post>";
  DisplayLabel("Brand Name: ") . DisplayTextbox("text","f_BrandName",10); 
  echo"<br>";
  DisplayLabel("Date Manufactured: ") . 
  DisplayTextbox("date","f_DateManufactured",5,date('Y-m-d')); echo"<br>";
  DisplayLabel("Camera: ") . DisplayTextbox("radio","f_Camera",5,"5 checked");
  echo "5MP";
  DisplayTextbox("radio","f_Camera",5,"10"); echo "10MP<br>";
  DisplayLabel("Colour: ") . DisplayTextbox("color","f_Colour",15);
  echo "<div class=\"datapair\">"; 
  DisplayButton("f_Save","Save","button_save-record.png"); 
  echo "</div>";
  echo "<div class=\"datapair\">";
  DisplayButton("f_Home","Home","button_home.png"); 
  echo "</div>";
  echo "</form>";
}

function SaveRecordToTableForm(&$mysqlObj,$TableName)
{
  $BrandName = $_POST["f_BrandName"];
  $DateManufactured = $_POST['f_DateManufactured'];
  $CameraMP = $_POST['f_Camera']; 
  $Colour = $_POST['f_Colour'];

	$stmt = $mysqlObj->prepare("INSERT INTO $TableName VALUES (?,?,?,?)");
	$stmt->bind_param("ssis",  $BrandName, $DateManufactured, $CameraMP,$Colour);
	if ($stmt->execute()) 
		echo "<p>Record Successfully added to $TableName</p>";
	else
		echo "<p>Unable to add record to $TableName</p>";
	$stmt->close();

  echo "<div class=\"datapair\">"; 
  DisplayButton("f_Home","Home","button_home.png"); 
  echo "</div>";
}

function displayDataForm(&$mysqlObj,$TableName)
{
  echo "<h2>Showing All Records</h2>";
	$SelectString = "Select $TableName.BrandName, DateManufactured, 
  CameraMP, Colour from Sunglasses";
	$stmt = $mysqlObj->prepare($SelectString); 
 	// no bind_param bec no data came from user
	$stmt->bind_result($BrandNameField, $DateManufacturedField, 
  $CameraMPField, $ColourField);
	$stmt->execute();
	while ($stmt->fetch())
	echo "
  <table>
    <tr>
      <th>Brand Name</th>
      <th>Date Manufactured</th>
      <th>Camera MP</th>
      <th>Colour</th>
    </tr>
    <tr>
      <td>$BrandNameField</td>
      <td>$DateManufacturedField</td>
      <td>$CameraMPField</td>
      <td><input type = color value = $ColourField></td>
    </tr> 
  </table>
  ";
 	$stmt->close();
  echo "<div class=\"datapair\">"; 
  DisplayButton("f_Home","Home","button_home.png");  
  echo "</div>";
}

function deleteRecordForm(&$mysqlObj,$TableName)
{
  echo "<form action = ? method=post>";
  DisplayLabel("WARNING! Deletion is Final! Which file would you like to 
  delete? ") . DisplayTextbox("text","f_DeleteFile",10); echo"<br>";
  echo "<div class=\"datapair\">"; 
  DisplayButton("f_IssueDelete","Delete!","button_delete.png");
  echo "</div>";
  echo "<div class=\"datapair\">"; 
  DisplayButton("f_Home","Home","button_home.png"); 
  echo "</div>";
  echo "</form>";
}

function issueDeleteForm ($mysqlObj,$TableName)
{
  echo "<form action = ? method=post>";
  $BrandName = $_POST["f_DeleteFile"];
  $deletion = new clsDeleteSunglassRecord();
  $deletion->deleteTheRecord($mysqlObj,$TableName,$BrandName);


  echo "$BrandName record Deleted";
  echo "</form>";
  echo "<div class=\"datapair\">"; 
  DisplayButton("f_Home","Home","button_home.png"); 
  echo "</div>";
}

// main
writeHeaders("Assignment One");
date_default_timezone_set ('America/Toronto');
$mysqlObj; 
$TableName = "Sunglasses"; 
// writeHeaders call  
if (isset($_POST['f_CreateTable']))
  createTableForm($mysqlObj,$TableName);
else if (isset($_POST['f_Save'])) saveRecordtoTableForm($mysqlObj,$TableName); 
  else if (isset($_POST['f_AddRecord'])) addRecordForm($mysqlObj,$TableName);	   
	  else if (isset($_POST['f_DeleteRecord'])) 
    deleteRecordForm($mysqlObj,$TableName) ;	 
      else if (isset($_POST['f_DisplayData'])) 
      displayDataForm ($mysqlObj,$TableName);
    		else if (isset($_POST['f_IssueDelete'])) 
        issueDeleteForm ($mysqlObj,$TableName);
		        else displayMainForm();
if (isset($mysqlObj)) $mysqlObj->close();
writeFooters();


?>