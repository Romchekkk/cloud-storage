<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('reload dump db');
$I->amOnPage('/');