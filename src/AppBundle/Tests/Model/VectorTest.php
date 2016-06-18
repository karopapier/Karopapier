<?php
 
include(dirname(__FILE__).'/../bootstrap/Propel.php');
 
$t = new lime_test(5, new lime_output_color());

$v=new Vector(-5,7);
$t->is($v->__toString(),'(-5|7)','->toString returns (|)');
 
$v=new Vector(2,2);
$order=array("(0|0)","(1|1)","(2|2)");
$t->is(array_keys($v->getPassedVectors()),$order,'->getPassedVectors returns correct passed vectors');

$v=new Vector(0,3);
$order=array("(0|0)","(0|1)","(0|2)","(0|3)");
$t->is(array_keys($v->getPassedVectors()),$order,'->getPassedVectors returns correct passed vectors');

$v=new Vector(-2,1);
$order=array("(0|0)","(-1|0)","(-1|1)","(-2|1)");
$t->is(array_keys($v->getPassedVectors()),$order,'->getPassedVectors returns correct passed vectors');

$v=new Vector(-3,-1);
$order=array("(0|0)","(-1|0)","(-2|-1)","(-3|-1)");
$t->is(array_keys($v->getPassedVectors()),$order,'->getPassedVectors returns correct passed vectors');