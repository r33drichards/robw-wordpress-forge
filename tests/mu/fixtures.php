<?php
// TEST ONLY. Excluded from install package. Never deploy.
if(!defined('ROBW_FORGE_LOCAL_TEST')||!ROBW_FORGE_LOCAL_TEST||!in_array(wp_parse_url(home_url(),PHP_URL_HOST),['127.0.0.1','localhost'],true))return;
add_action('template_redirect',function(){if(wp_parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)==='/__forge_test__/reset'){global $wpdb;$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",$wpdb->esc_like(\RobwForge\Store::PREFIX).'%'));echo 'reset';exit;}if(preg_match('~^/__forge_test__/login/(alice|bob|admin)$~D',wp_parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),$m)){$u=get_user_by('login',$m[1]);wp_set_auth_cookie($u->ID);wp_redirect(home_url('/forge'));exit;}if(wp_parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)==='/__forge_test__/nonce'){header('Content-Type: application/json');echo wp_json_encode(['nonce'=>wp_create_nonce('robw-forge'),'uid'=>get_current_user_id(),'php'=>PHP_VERSION,'wordpress'=>$GLOBALS['wp_version']]);exit;}if(wp_parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)==='/__forge_test__/due'){foreach(\RobwForge\Store::list_ids()as$id)\RobwForge\Store::mutate($id,function(&$r){foreach($r['events']as&$e)foreach($e['deliveries']as&$d)$d['next']=0;});echo 'ok';exit;}},-200);

add_action('template_redirect',function(){if(wp_parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)!=='/__forge_test__/codec')return;
 $G='RobwForge\\Git';$checks=[];$reject=function($name,$fn)use(&$checks){try{$fn();$checks[$name]=false;}catch(InvalidArgumentException $e){$checks[$name]=true;}};
 $reject('truncated_packet',function()use($G){$p=0;$G::packets('000aabc',$p);});
 $reject('invalid_packet_hex',function()use($G){$p=0;$G::packets('zzzz',$p);});
 $reject('invalid_control_packet',function()use($G){$p=0;$G::packets('0001',$p);});
 $reject('delta_opcode_zero',function()use($G){$G::delta('a',"\x01\x01\x00");});
 $reject('delta_wrong_base',function()use($G){$G::delta('a',"\x02\x01\x01b");});
 $reject('delta_copy_range',function()use($G){$G::delta('a',"\x01\x01\x91\x02\x01");});
 $reject('delta_integer_overflow',function()use($G){$G::delta('a',str_repeat("\xff",9));});
 $reject('tree_dotgit',function()use($G){$G::tree('100644 .Git'."\0".str_repeat("\0",20));});
 $reject('tree_dotdot',function()use($G){$G::tree('100644 ..'."\0".str_repeat("\0",20));});
 $reject('tree_truncated',function()use($G){$G::tree('100644 a'."\0".'abc');});
 $reject('tree_wrong_order',function()use($G){$G::tree('100644 b'."\0".str_repeat("\0",20).'100644 a'."\0".str_repeat("\0",20));});
 $reject('corrupt_object',function()use($G){$G::get([str_repeat('a',40)=>['type'=>'blob','data'=>base64_encode('changed')]],str_repeat('a',40));});
 $o=[];$id=$G::put($o,'blob',"binary\0\xffbytes");$pack=$G::pack($o);$checks['binary_pack_roundtrip']=$G::unpack($pack,[])===$o;
 $bad=$pack;$bad[strlen($bad)-1]=chr(ord($bad[strlen($bad)-1])^1);$reject('checksum_mutation',function()use($G,$bad){$G::unpack($bad,[]);});
 foreach(['version'=>pack('NN',1,0),'count'=>pack('NN',2,1025)]as$name=>$head){$b='PACK'.$head;$b.=sha1($b,true);$reject('pack_'.$name,function()use($G,$b){$G::unpack($b,[]);});}
 $b='PACK'.pack('NN',2,1)."\x30".gzcompress(str_repeat('x',2000000));$b.=sha1($b,true);$reject('bounded_inflate_bomb',function()use($G,$b){$G::unpack($b,[]);});
 $checks['delta_valid_literal']=$G::delta('a',"\x01\x01\x01b")==='b';
 header('Content-Type: application/json');echo wp_json_encode($checks);exit;
},-200);
