<?php

//http://www.youtube.com/watch?v=-Rq8f6Pyc4E
//$LOGGING_ENABLED = false;
//$LOGGING_STREAM = 3;
//$LOGGERS_LIST = array('Autoload');

require_once 'sdk/MainImportAdmin.php';
require_once 'ajax/actions/AbstractAjaxAction.php';
header('Content-Type: text/html; charset=utf-8');
ExceptionHandler::registerPretty();

class PsShotdown extends PsShotdownSdk {

    const MyDestr = 5;
    const MyDestr2 = 3;

}

class C1 implements Destructable {

    public function onDestruct() {
        echo __CLASS__;
        br();
    }

}

class C2 implements Destructable {

    public function onDestruct() {
        echo __CLASS__;
        br();
    }

}

PsShotdown::registerDestructable(new C1(), PsShotdown::MyDestr);
PsShotdown::registerDestructable(new C2(), PsShotdown::MyDestr2);

die;


echo TestUtils::testProductivity(function() {
            print_r(AdminDbBean::inst()->getColumns('blog_rubric'));
        }, 1);



die;
//PSDB::insert('insert into ps_test_data_load (v_key, v_value) values (?, ?)', array('key1', 'val1'));

echo file_get_contents('http://www.dasdasdasdasdas.ru');

die;

print_r(PsDbIniHelper::makeDbIniForSchema(ENTITY_SCOPE_SDK));

die;


$conn = ADONewConnection('mysql://root:1111@localhost');

//Зададим некоторые настройки
$conn->debug = ADODB_DEBUG;
$conn->query("SET NAMES 'utf8'");
$conn->query("SET CHARACTER SET 'utf8'");

print_r($conn->execute('SHOW DATABASES')->GetArray());

die;

PsConnectionPool::configure(PsConnectionParams::get(PsConnectionParams::CONN_PRODUCTION, ENTITY_SCOPE_SDK));


die;

$file = 'C:\www\postupayu.ru\test.bat';

$out = shell_exec($file);
echo $out;
die;


$out = shell_exec("mysql --default-character-set=utf8 --user=root --password=1111 < www\database\temp\ps_test.sql");

die;


$ctt = file_get_contents('c:\www\postupayu.ru\db\sdk\schema.sql');

function parse($docblock) {
    $annotations = array();
    // Strip away the docblock header and footer to ease parsing of one line annotations

    if (preg_match_all('/CREATE TABLE ([A-Za-z_-]+)?[ \t]*\r?$/m', $docblock, $matches)) {
        print_r($matches);
    }

    return $annotations;
}

parse($ctt);

die;

$table = AdminDbBean::inst()->getTables()['issue_post'];

print_r(PsTableColumnProps::getForTable($table));
die;

class MyAnn1 {
    /**
     * My simple class MY_CONST - MyAnn1
     * 
     * @abstract yes-MY_CONST
     * @access public-MY_CONST
     * @access public-MY_CONST
     * @author azazello-MY_CONST
     */

    const MY_CONST1 = 'MyAnn1';

    /**
     * My simple class test1
     * 
     * @abstract yes-test1
     * @access public-test1
     * @access public-test1
     * @author azazello-test1
     */
    public static function test3() {
        echo get_called_class();
    }

}

class MyAnn2 extends MyAnn1 {
    /**
     * My simple class MY_CONST - MyAnn1
     * 
     * @abstract yes-MY_CONST
     * @access public-MY_CONST
     * @access public-MY_CONST
     * @author azazello-MY_CONST
     */

    const MY_CONST2 = 'MyAnn2';

    public static function test1() {
        self::test3();
    }

}

MyAnn2::test1();

die;

/**
 * @param  string $docblock
 * @return array
 * @since  Method available since Release 3.4.0
 */
function parseAnnotations($docblock) {
    $annotations = array();
    // Strip away the docblock header and footer to ease parsing of one line annotations
    $docblock = substr($docblock, 3, -2);

    if (preg_match_all('/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m', $docblock, $matches)) {
        $numMatches = count($matches[0]);

        for ($i = 0; $i < $numMatches; ++$i) {
            $annotations[$matches['name'][$i]][] = $matches['value'][$i];
        }
    }

    return $annotations;
}

br();
//print_r(parseAnnotations(PsUtil::newReflectionClass('MyAnn')->getMethod('test1')->getDocComment()));
br();
print_r(PsUtil::newReflectionClass('MyAnn1')->getMethod('test1')->getDocComment());
br();


die;

echo PSForm::inst()->isSdk();

br();
br();
print_r(array_keys(FoldingsStore::inst()->getFoldings()));
br();
print_r(array_keys(FoldingsStore::inst()->getProviders()));
br();
print_r(array_keys(FoldingsStore::inst()->getFoldings(ENTITY_SCOPE_PROJ)));
br();


//print_r(FoldedResourcesManager::inst()->allFoldings());

die;

class MyAnn {
    /**
     * My simple class MY_CONST
     * 
     * @abstract yes-MY_CONST
     * @access public-MY_CONST
     * @access public-MY_CONST
     * @author azazello-MY_CONST
     */

    const MY_CONST = 'xxx';

    /**
     * My simple class test1
     * 
     * @abstract yes-test1
     * @access public-test1
     * @access public-test1
     * @author azazello-test1
     */
    public static function test1() {
        
    }

    /**
     * My simple class test2
     * 
     * @abstract yes-test2
     * @access public-test2
     * @access public-test2
     * @author azazello-test2
     */
    public static function test2() {
        
    }

}

print_r(ConfigIni::getIni());
print_r(ConfigIni::getGroup('db-production'));
var_export(ConfigIni::hasGroup('db-production1'));

print_r(ConfigIni::getProp('db-production', 'url'));
var_export(ConfigIni::hasProp('db-production', 'url1'));

die;

$di = DirItem::inst(null, 'php.properties');
$s = Secundomer::startedInst();
// Обрабатываем без секций
print_r($di->parseAsIni(true));

var_dump($di->parseAsIni(true));

echo $s->stop()->getTime();
die;

$words = array('Земля');

foreach ($words as $word) {
    print_r(InflectsManager::inst()->getInflections($word));
}

die;

print_r(PSMemCache::inst()->getversion());
br();

//PSMemCache::inst()->set('my data', PL_atom::inst());

if (PSMemCache::enabled()) {
//    PSMemCache::inst()->set('my data', array(1, 2, 3));
    print_r(PSMemCache::inst()->get('my data')->getDescr());
}

//$memcache_obj->set('Image', 1);

die;

die(print_r(PSDB::getArray('select * from users'), true));

die;
$text = '5f039b4ef0058a1d652f13d612375a5b';

echo $text;
br();
echo PsCheck::isMd5($text) ? 'valid' : 'invalid';
die;


$a = array('a' => 1, 'b' => 2, 'c' => 3);
$ad = ArrayAdapter::inst($a);
$ad->set('a', 2);
$ad->set('a', 3, true);
$ad->set('a', 4, true);
$ad->set('a', 5, true);
$ad->restoreStory();
$ad->set('a', 3);
$ad->set('a', 4, true);
$ad->set('a', 5, true);
$ad->restoreStory();
echo $ad;



die;

$a = array('a' => 1, 'b' => 2, 'c' => 3);
$ad = ArrayAdapter::inst($a, true);
$ad->remove('a');
print_r($a);
echo $ad;


die;

$_GET['a'] = 1;
$_GET['b'] = 2;

print_r($_GET);

GetArrayAdapter::inst()->remove('a');
GetArrayAdapter::inst()->set('d', 4);

print_r($_GET);

die;



$_SERVER['a'] = 1;
$_SERVER['b'] = 2;

print_r($_SERVER);

ServerArrayAdapter::inst()->remove('a');
ServerArrayAdapter::inst()->set('d', 4);

print_r($_SERVER);

die;

$a = array('a' => 1, 'b' => 2, 'c' => 3);

print_r($a);

$adapter = ArrayAdapter::inst($a, true);
$adapter->remove('a');
$adapter->set('d', 4);

print_r($a);

die;


$arr = array('a', 'b', 'c', 'd');
ArrayAdapter::inst(ArrayAdapter::inst($arr))->remove(0);
ArrayAdapter::inst(ArrayAdapter::inst($arr))->remove(1);
ArrayAdapter::inst($arr)->remove(2);
print_r($arr);



die;

echo PsUrl::toHttp();

die;

echo DirItem::inst('kitcore/dirs/\\///')->getSibling('logger.xxx')->getNameNoExt();

die;

echo PsStrings::replaceWithParams('+', 'a+b+c+d+e+f', array(1, 2, 3, 4, 5), true);
br();
echo PsStrings::replaceWithParams('+', '', array(1, 2, 3, 4, 5));

die;

//echo StringUtils::replaceByTurn('/x/', '1', '2', 'a x b x c x a x b x c');
echo PregReplaceCyclic::replace('/x/', 'a x b x c x a x b x c', array(1, 2));
br();
echo PregReplaceCyclic::replace('/x/', 'a x b x c x a x b x c', array(1, 2, 3));
br();
echo PregReplaceCyclic::replace('/x/', 'a x b x c x a x b x c', array(1, 2, 3, 4));
br();

die;

echo PsRand::string(MD5_STR_LENGTH, null, false);

die;

echo PsImg::isImg(DirItem::inst('/resources/images/todo.png')->getAbsPath());
echo PsImg::isImg(DirItem::inst('/resources/images/tile.gif')->getAbsPath());
echo PsImg::isImg(DirItem::inst('/resources/images/author.jpg')->getAbsPath());
echo PsImg::isImg(DirItem::inst('/resources/images/author.jpg')->getAbsPath());

die;

print_r(getimagesize(DirItem::inst('/resources/images/todo.png')->getAbsPath()));
br();
print_r(getimagesize(DirItem::inst('/resources/images/tile.gif')->getAbsPath()));
br();
print_r(getimagesize(DirItem::inst('/resources/images/author.jpg')->getAbsPath()));
//IMAGETYPE_

die;

echo image_type_to_extension(IMAGETYPE_PNG, false);
br();
echo image_type_to_extension(IMAGETYPE_GIF, false);
br();
echo image_type_to_extension(IMAGETYPE_JPEG, false);
br();
echo image_type_to_extension(IMAGETYPE_JP2, false);
br();
echo image_type_to_mime_type(IMAGETYPE_JPEG);

die;

print (int) ((0.1 + 0.7) * 10);
die;

echo PsSequence::LOG()->current();
br();
echo PsSequence::LOG()->next();
br();
echo PsSequence::LOG()->current();
br();
echo PsSequence::LOG()->isCurrent(30);

die;

echo DirItem::inst(null)->getModificationTime();

die;
echo PageContext::inst()->isAjax();

die;

PsUtil::startUnlimitedMode();

for ($i = 0; $i <= 30000; $i++) {
    MailAudit::inst()->afterSended(PsMailSender::inst());
}

die;

echo PsDefines::getTableDumpPortion();

die;

$where[] = Query::assocParam('dt_event', time() - 5000000, true, '>=');
AdminTableDump::dumpTable('id_rec', 'ps_audit', $where);

die;

echo Query::delete('ps_audit', $where);

die;

//print_r(AdminAuditBean::inst()->getProcessStatistic());

print_r(AdminAuditTools::getAuditStatistic());

die;

DbProp::TESTB()->set(null);

echo DbProp::TESTB()->get();

die;

foreach (array('boolean', 'integer', 'double', 'float', 'string', 'array', 'object', 'resource', 'null', 'unknown type') as $type) {
    echo "const PHP_TYPE_" . strtoupper($type) . " = '$type';";
    br();
}


die;

$zipDi = DirManager::autogen('db-dumps')->getDirItem(null, 'my-table', 'zip')->remove();
$zip = $zipDi->startZip();

$zip->addFromString('data', serialize(PSDB::getArray('select * from ps_audit')));
$zip->close();

die;


$sender = PsMailSender::inst();
$sender->SetSubject('My message subject');
$sender->AddAttachment(DirManager::mmedia()->getDirItem('foto', '1.jpg')->getAbsPath());
$sender->SetBody('My message body');
//$sender->SetFrom('azaz@mail.ru', 'От azaz');
$sender->AddAddress('azazello85111111@mail.ru', 'Илье');

PsUtil::startUnlimitedMode();

for ($i = 0; $i < 57; $i++) {
    MailAudit::inst()->afterSended($sender);
}

die;



$sender = PsMailSender::inst();
$sender->SetSubject('My message subject');
$sender->AddAttachment(DirManager::mmedia()->getDirItem('foto', '1.jpg')->getAbsPath());
$sender->SetBody('My message body');
//$sender->SetFrom('azaz@mail.ru', 'От azaz');
$sender->AddAddress('azazello85111111@mail.ru', 'Илье');
//$sender->AddCC('ivanov.ilya.alex@gmail.com', 'Alexu');
//$sender->AddBCC('79031512358@yandex.ru');
$sender->Send();

die;
$ids = array(1, '4', 2, 3, 3, 4, 4, 1, '2', 5, 6, 7);

foreach (Query::assocParamsIn('id', $ids, 3) as $param) {
    echo $param;
    br();
};

die;


PsMailSender::fastSend('azaz@mail.ru', 'azaz@mail.ru', 'azaz@mail.ru');
die;

$name = 'xxx';

$$name = 6;

define($name, null);

PsDefinesEngine::set($name, 1, PsDefinesEngine::TYPE_GD);
PsDefinesEngine::savepointStart();

PsDefinesEngine::set($name, 2, PsDefinesEngine::TYPE_GD);
PsDefinesEngine::set($name, 3, PsDefinesEngine::TYPE_GD);
//PsDefinesEngine::savepointRestore();
//PsDefinesEngine::restore($name);
//PsDefinesEngine::restore($name);
echo PsDefinesEngine::get($name, PsDefinesEngine::TYPE_GD);
die;

define('psx', null);

echo defined('psx');

die;

PsDefineVar::REPLACE_FORMULES_WITH_IMG();

die;

$qparams['a'] = 1;
$qparams[] = Query::assocParam('b', 'unix_timestamp()', false);
$qparams[] = Query::assocParam('c', 3);
$qparams['d'] = null;
$qparams[] = Query::assocParam('e', null);
$qparams[] = Query::assocParam('f', null);
echo Query::insert('users', $qparams)->build($params);
br();
print_r($params);

br();
br();


$qparams[] = Query::assocParam('trace', true, true, '!=');

$order[] = 'a';
$order[] = array('b', array('d', 'e'), 'c');
$select = Query::select('$what', '$table', $qparams, 'group', $order, '1');
$select->setWhere('b', 3);
$select->addWhere(Query::assocParam('n_order', 100500, true, '>'));
$select->addWhere('my is not null');
//$select->addWhere(QueryParam::plain('x is not null'));
$select->addWhere(array(Query::assocParam('t', 'unix_timestamp()', false)));

echo $select->build($params);
br();
print_r($params);

die;

$columns = array(1);
if (empty($columns)) {
    echo 'empty';
}
Query::assertOnlyAssocParams($params);
die;

$upDown = true;
$orderRoot = 'id_root ' . ($upDown ? 'asc' : 'desc');
$orderMsgs = array('dt_event asc', 'myColumn asc');
$order = array($orderRoot, $orderMsgs);


$select = Query::select('$what', '$table', array('a' => 1, Query::assocParam('x', "'y'", false)), 'group', $order, '1');
$select->setWhere('b', 3);
$select->addWhere(Query::assocParam('n_order', 1, true, '>'));
$select->addWhere('my is not null');
//$select->addWhere(QueryParam::plain('x is not null'));
$select->addWhere(array(Query::assocParam('t', 'unix_timestamp()', false)));

echo $select->build($params);
br();
print_r($params);
die;



$a = array('a', 'b', 'c');
$b = array('b', 'c', 'd');

print_r(array_intersect($a, $b));

die;

FORM_RegForm::getIdent();

$data = new RegFormData();
$data->setAbout('aaaa');
$data->setMsg('My msg');
$data->setPassword('My pwd');

print_r($data->asAssocArray(array('passwd')));


die;

echo Query::select('*', 'users')->addWhere('a=1')->addWhere(array('a' => 1, 'b=2'))->setWhere('c', 3)->setGroup('id_user')->setLimit(2)->build($params);
br();
print_r($params);
br();

echo Query::update('users')->addWhat('a=3')->addWhat(array('U' => 15))->addWhere('a=1')->addWhere(array('a' => 1, 'b=2'))->setWhere('c', 3)->build($params1);
br();
print_r($params1);

die;

$a = 'ваывыф
    вфывфывыф
    выфвфы';
$b = 'ваывыф
    вфывфывыф
    выфвфы';

echo strcmp($a, $b);

die;

interface XXXX {

    function xxxx();
}

PsUtil::assertClassExists('XXXX');
die;

$a = 1.1;
PsCheck::int($a);

die;
echo MyEnum::VAL1();

die;

$t = 'abc';

PsCheck::intOrNull($t, 'aaaa');

die;

echo UserLoadType::CLIENT()->getRestriction();

die;

$text = "<act>2</act>\r\n<agent_date>2009-04-15T11:22:33</agent_date>\r\n<pay_id>2345</pay_id>\r\n<pay_date>2009-04-15T11:00:12</pay_date>\r\n<account>54321</account>\r\n<pay_amount>10000</pay_amount>\r\n<month>08.2012</month>secret";
echo md5($text);

die;
PsUtil::assertClassHasDifferentConstValues('UserLoginsAudit', 'CODE_');

die;

$json = PSDB::getRec('select * from ps_user_logins where id_action=?', 143);

$arr = json_decode($json['v_agent'], true);
print_r($arr['agent']);

die;


$data = array('ip' => ServerArrayAdapter::REMOTE_ADDR(), 'agent' => ServerArrayAdapter::HTTP_USER_AGENT());

//PSDB::insert('insert into ps_user_logins (id_user, dt_event, n_action, v_agent) values (1, unix_timestamp(), 1, ?)', json_encode($data));


die;

$text = md5("23123") . 'l';
//$text = "860b432652504fa60f8dA94398e20de";

echo $text;
br();
echo DirManager::inst()->getHashedDirItem(null, $text);
br();
echo TexImager::inst()->decodeTexFromHash(TexTools::formulaHash('\sqrt{x}'));

die;

echo DialogManager::inst()->getDialog('plugins')->getWindowContent();

die;

$rc = new ReflectionClass('AdminFoldedManager');
$lines = DirItem::inst(Autoload::inst()->getClassPath('AdminFoldedManager'))->getFileLines(true, true);

$lines = array_slice($lines, $rc->getStartLine(), $rc->getEndLine() - $rc->getStartLine() - 1);
print_r($lines);



die;

$method = PhpBuilderMethod::inst('myMethod');
$method->setComment(array('My comment', 'Next line'));
$method->addAnnotation('return', 'My class');
$method->setIsFinal(true);
//$method->setIsAbstract(true);
$method->setBody('return "xxxxxx";');
echo $method;


die;

print_r(PhpClassAdapter::inst('BubbledFolding')->getDi()->getFileContents());

die;


/* @var $inf PhpClassAdapter */
foreach (AdminFoldedManager::inst()->getFoldedInterfaces() as $inf) {
    /* @var $inf PhpMethodAdapter */
    foreach ($inf->getMethodAdapters() as $meth) {
        echo $meth->getHtmlDescr();
        br();
        br();
    }
}

die;

$a = array('a' => 1, 'b' => 2);
$b = $a;

unset($a['a']);

print_r($b);
print_r($a);

die;

echo TexTools::replaceTeX('a b c notex y z', function($original, $content, $isBlock) {
            print_r($original);
            br();
        }, true);

die;

echo UserInputTools::safeLongText('А вот тут экран & > <, а вот тут уже нет: \[a & b > c\], вот так:)');

die;

$text = 'Текст \[\pi\] с \(\pi\) формулами \(\sqrt{x}\)';


$extractor = TexExtractor::inst($text, true);
print_r($extractor->getMaskedText());

$masked = $extractor->getMaskedText();
$masked = str_replace('формулами', '\[\sqrt{y}\]', $masked);
br();
print_r($extractor->restoreMasks($masked));


die;


$insts = PsMessages::insts();
/** @var MessageResource */
$mr = $insts[0];

//echo CommonMessages::texErrorInlineNotOpen('aga', 'baga');


die;

// Задаем текущий язык проекта
putenv("LANG=ru_RU");

// Задаем текущую локаль (кодировку)
setlocale(LC_ALL, "English");

// Указываем имя домена
$domain = 'my_site';

// Задаем каталог домена, где содержатся переводы
bindtextdomain($domain, PATH_BASE_DIR . "/locale");

// Выбираем домен для работы

textdomain($domain);

// Если необходимо, принудительно указываем кодировку
// (эта строка не обязательна, она нужна,
// если вы хотите выводить текст в отличной от текущей локали кодировке).
bind_textdomain_codeset($domain, 'UTF-8');

echo _('Welcome to My PHP Application - NEW!');

die;

echo TestUtils::testProductivity(function() {
            PSDB::getRec('select count(1) from users where id_user=?', 1);
        });
br();
echo TestUtils::testProductivity(function() {
            PSDB::getRec('select 1 from users where id_user=? limit 1', 1);
        });


die;
print_r(PSDB::getArray('select * from users', null, IndexedArrayQueryFetcher::inst('id_user')));

die;

new PsMailSender();
PsMailSender::inst();
PsMailSender::inst();
die;

PsMailSender::fastSend('Тестовое письмо', 'Его тело', '79031512358@yandex.ru', 'Илья');
die;

$send = PsMailSend::inst();
$send->AddAddress('79031512358@yandex.ru', 'Илья');
$send->AddCC('azazello85@mail.ru', 'Копия для Ильи');
$send->SetSubject('Тема письма');
$send->SetBody('Тело письма');
$send->Send();

echo $send;
die;

//TestManager::inst()->genereteTestUsers(1000);

echo TestUtils::testProductivity(function() {
            PSDB::getArray('select * from users where id_user=101');
            PSDB::getArray('select * from users where id_user=102');
            PSDB::getArray('select * from users where id_user=103');
            PSDB::getArray('select * from users where id_user=104');
            PSDB::getArray('select * from users where id_user=105');
            PSDB::getArray('select * from users where id_user=106');
            PSDB::getArray('select * from users where id_user=107');
            PSDB::getArray('select * from users where id_user=108');
            PSDB::getArray('select * from users where id_user=109');
        });
br();

echo TestUtils::testProductivity(function() {
            PSDB::getArray('select count(1) from users where id_user=101');
            PSDB::getArray('select count(1) from users where id_user=102');
            PSDB::getArray('select count(1) from users where id_user=103');
            PSDB::getArray('select count(1) from users where id_user=104');
            PSDB::getArray('select count(1) from users where id_user=105');
            PSDB::getArray('select count(1) from users where id_user=106');
            PSDB::getArray('select count(1) from users where id_user=107');
            PSDB::getArray('select count(1) from users where id_user=108');
            PSDB::getArray('select count(1) from users where id_user=109');
        });

br();
echo TestUtils::testProductivity(function() {
            PSDB::getArray('select * from users where id_user in (101, 102, 103, 104, 105, 106, 107, 108, 109)');
        });

br();
echo TestUtils::testProductivity(function() {
            PSDB::getArray('select * from users where id_user in (101)');
        });

br();
echo TestUtils::testProductivity(function() {
            PSDB::getArray('select * from users where id_user = 101');
        });

die;

$code = UserCodesBean::inst()->generateAndSave('R', 1);
UserCodesBean::inst()->markCodeAsUsed('R', $code, 1);
UserCodesBean::inst()->dropUnusedCodes('R', 1);
br();

die;

print_r(PSDB::getRec('select * from ps_user_codes limit 1'));
br();
echo reset(PSDB::getRec('select * from ps_user_codes limit 1'));
die;

//PsMail::inst()->content('Привет!', 'Это - письмо восстановления и на него <b>не нужно отвечать!</b>')->to('azazello85@mail.ru', 'Илья Иванов')->bcc('postupayu@yandex.ru', 'Админу')->send();
//echo WebPage::inst(PAGE_PASS_REMIND)->getUrl(true, array('code' => 'xxxxxx'));
echo PsRand::string();
die;

$u = 0;
$l = 0;
$num = 0;
for ($index1 = 0; $index1 < 1000; $index1++) {
    $char = PsRand::char(null, true);
    if (is_numeric($char)) {
        ++$num;
        continue;
    }
    if (ps_is_lower($char)) {
        ++$l;
    } else {
        ++$u;
    }
}
echo "Num: $num, l: $l, u: $u";
die;

MagManager::inst();
MagManager::inst();
MagManager::inst();
MagManager::inst();
die;

$str = '862430dd-7f69-4d3d-b243-52dc22de6ecb;109;14849548;2014-03-17T08:47:17;10.00;RUB;10.00;RUB;3;ulae7baSfaeMeih2';
echo base64_encode(md5($str, true));

die;

file_append_contents('c:/xxx/1.txt', 'A');

die;

$data = PSDB::getArray('select * from blog_post limit 100');
echo count($data);
br();

function convert($size) {
    $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

$DIRECT = RequestArrayAdapter::inst()->bool('V1');

$RC = new ReflectionClass('Post');
$constructor = array('is');
$sec = Secundomer::inst();
if ($DIRECT) {
    foreach ($data as $row) {
        $sec->start();
        $RC->newInstance('is', $row);
        $sec->stop();
    }
} else {
    foreach ($data as $row) {
        $sec->start();
        $params = $constructor;
        $params[] = $row;
        $params[1]['v_name'] = 'xxx';
        $RC->newInstanceArgs($params);
        unset($params);
        $sec->stop();
    }
}

echo ($DIRECT ? 'V1' : 'V2') . ', Memory: ' . memory_get_usage() . ' (' . convert(memory_get_usage()) . '), Time: ' . $sec->getTotalTime();

die;

class A {

    public function test() {
        
    }

}

interface I {
    
}

class C extends A implements I {

    public function test() {
        
    }

}

$class = new ReflectionClass('A');
echo $class->getMethod('test1')->getDeclaringClass()->getName();

die;

echo PsUtil::isInstanceOf($class, 'A');
br();
echo PsUtil::isInstanceOf($class, 'C');
br();
echo PsUtil::isInstanceOf($class, 'I');
br();
echo PsUtil::isInstanceOf('C', 'A');
br();
echo PsUtil::isInstanceOf('C', 'C');
br();
echo PsUtil::isInstanceOf('C', 'I');
br();
echo PsUtil::isInstanceOf('A', 'I');
br();
echo PsUtil::isInstanceOf('I', 'A');
br();



die;

$rc = new ReflectionClass(TestManager::inst());
echo $rc->getFileName();


die;

$str = "R490910732391;
2088;
14185310;
2014-02-25T20:29:57;
600.00;
RUB;
600.00;
RUB;
31;
;
Dseki58dkstj324f";

echo md5($str, true);
br();

echo base64_encode(md5($str, true));

die;

$vote = rand(-1, 1);

echo "($vote)";
br();

echo $vote > 0 ? ' active' : ($vote == 0 ? ' clickable' : '');
br();
echo $vote == 0 ? '' : ' clickable';
br();
echo $vote < 0 ? ' active' : ($vote == 0 ? ' clickable' : '');
br();



die;

define('PS_M_T', microtime(true));


echo PS_M_T;

die;

UP_fromadmin::inst()->givePoints(PsUser::inst(100), 5, 'Вы молодец! ' . getRandomString());


die;

UP_fromadmin::inst()->givePoints(PsUser::inst(100), 5, 'Вы молодец! ' . getRandomString());

/*
  drop table `issue_post_comments`;
  CREATE TABLE `issue_post_comments` (
  `id_comment` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` int(10) unsigned DEFAULT NULL,
  `id_root` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned DEFAULT NULL,
  `dt_event` int(10) unsigned NOT NULL,
  `content` text NULL,
  `v_theme` varchar2(255) NULL,
  `b_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `b_known` tinyint(1) NOT NULL DEFAULT '0',
  `b_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `n_deep` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id_comment`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1155 DEFAULT CHARSET=utf8;

 */

PSDB::update('update issue_post_comments set id_parent=null');
PSDB::update('delete from issue_post_comments');

$postId = 1;

function getComment() {
    return TestManager::inst()->getText(TrainManager::inst(), 8, true);
}

function getUserToId($parentId) {
    return array_get_value('id_user', PSDB::getRec('select id_user from issue_post_comments where id_comment=?', $parentId));
}

for ($index = 0; $index < 5; $index++) {
    $rootId = array_get_value('ID', PSDB::getRec('select IFNULL(MAX(id_comment), 0) + 1 as ID FROM issue_post_comments'));

    PSDB::update('INSERT INTO issue_post_comments (id_comment, id_parent, id_root, id_user, dt_event, content, n_deep, id_post) 
    VALUES (?, null, ?, ?, ?, ?, ?, ?)', array($rootId, $rootId, TESTBean::inst()->getRandomUserId(), $index, getComment(), 1, $postId));
}

for ($index = 0; $index < 20; $index++) {
    $arr = PSDB::getArray('select id_comment from issue_post_comments where id_parent is null order by RAND() limit 1');
    $rootId = $arr[0]['id_comment'];

    $userToId = getUserToId($rootId);
    $userId = $userToId;
    while ($userId == $userToId) {
        $userId = TESTBean::inst()->getRandomUserId();
    }

    PSDB::insert('INSERT INTO issue_post_comments (id_parent, id_root, id_user, id_user_to, dt_event, content, n_deep, id_post) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)', array($rootId, $rootId, $userId, $userToId, rand(100, 500), getComment(), 2, $postId));
}

for ($index = 0; $index < 20; $index++) {
    $arr = PSDB::getArray('select id_comment, id_root from issue_post_comments where id_parent is not null order by RAND() limit 1');
    print_r($arr);
    $rootId = $arr[0]['id_root'];
    $parentId = $arr[0]['id_comment'];

    $userToId = getUserToId($parentId);
    $userId = $userToId;
    while ($userId == $userToId) {
        $userId = TESTBean::inst()->getRandomUserId();
    }

    PSDB::insert('INSERT INTO issue_post_comments (id_parent, id_root, id_user, id_user_to, dt_event, content, n_deep, id_post) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)', array($parentId, $rootId, $userId, $userToId, rand(501, 1000), getComment(), 3, $postId));
}

//(id_parent, id_root, id_user, dt_event, 'content', b_deleted, b_confirmed, n_deep)


die;

$t = PSForm::inst();
echo @(int) $t;

die;

PsUser::inst(100)->setAvatar(80);
echo PsHtml::img(array('src' => PsUser::inst(100)->getAvatarDi('100x100')));


die;

PsUser::inst()->deteleAvatar(75);
PsUser::inst()->deteleAvatar(75);

die;

$diSrc = DirItem::inst('/resources/images/tile80.png');
PsImgEditor::resize($diSrc, '100x');
PsImgEditor::resize($diSrc, '120x');
$di = PsImgEditor::resize($diSrc, '150x');
echo PsHtml::img(array('src' => $di));

br();

$diSrc2 = DirItem::inst('/resources/images/author4.jpg');
PsImgEditor::resize($diSrc2, '100x');
PsImgEditor::resize($diSrc2, '120x');
$di2 = PsImgEditor::resize($diSrc2, '150x');
echo PsHtml::img(array('src' => $di2));


//PsImgEditor::copy($diSrc, $to);
PsImgEditor::clean($diSrc);



die;

UploadsBean::inst()->deleteFile('A', 49, 2);

print_r(UploadsBean::inst()->getFilesIds('A', 1));

die;

class XXX {

    function fill(&$params) {
        unset($params['b']);
        $params = array_values($params);
    }

    function printRes($str, $params) {
        echo $str . print_r($params, true);
    }

    function test() {
        $params['a'] = 1;
        $params['b'] = 2;

        $this->printRes('select ' . $this->fill($params), $params);
    }

}

$a = new XXX();
$a->test();

die;

PsUtil::defineClassConsts('PsConstJs', 'JS');

echo constant('JS_PAGE_JS_GROUP_PANELS');


die;

class X1 {

    protected function test() {
        echo 'X1';
    }

}

class X2 extends X1 {

    public function test() {
        parent::test();
    }

}

$x = new X2();
$x->test();

die;

//echo PsUtil::isInstanceOf('AvatarUploader', 'FileUploader');

class XXX {

    private $a;

    function test() {
        
    }

    function printState() {
        $this->a = $this->test();
        var_dump($this->a);
    }

}

$x = new XXX();
$x->printState();

die;

print_r(FileUploader::classNames());
?>