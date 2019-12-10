<?php
require_once 'idiorm.php';
ORM::configure('sqlite:./example.db');
ORM::configure('return_result_sets', true);
require 'flight/Flight.php';

// title_ins_exe ##################################################
Flight::route('/title_ins_exe', function(){
	$result = ORM::for_table('title')->create();
	$result->date = date('Y-m-d');
	$result->title = Flight::request()->data->title;
	$result->text = Flight::request()->data->text;
	$result->updated = time();
	$result->save();
	Flight::redirect('/title');
});

// title_upd ##################################################
Flight::route('/title_upd', function(){
	$results = ORM::for_table('title')->find_one(Flight::request()->query->id);
	$str = "";
	$str .= "<form action='title_upd_exe' method='post'>";
	$str .= "<input type='hidden' name='id' value=" . Flight::request()->query->id . ">";

	$str .= "<input type='text' name='date' value='";
	$str .= $results->date;
	$str .= "'><br>";

	$str .= "<input type='text' name='title' value='";
	$str .= $results->title;
	$str .= "'><br>";


	$str .= "<input type='text' name='text' value='";
	//$str .= nl2br(htmlspecialchars($results->text),false);
	$str .= $results->text;
	$str .= "'><br>";

	$str .= "<input type='submit' value='send'>";
	$str .= "</form>";

	Flight::render('header', array('heading' => 'Hello'), 'header_content');
	Flight::render('body', array('str' => $str), 'body_content');
	Flight::render('layout', array('title' => 'todo'));
});

// title_upd_exe ##################################################
Flight::route('/title_upd_exe', function(){
	$results = ORM::for_table('title')->find_one(Flight::request()->data->id);
	$date = Flight::request()->data->date;
	$title = Flight::request()->data->title;
	$results->date = $date;
	$results->title = $title;
	$results->text = Flight::request()->data->text;
	$results->save();
	Flight::redirect('/title');
});

// title_up ##################################################
Flight::route('/title_up', function(){
	//$results = ORM::for_table('test')->find_one(Flight::request()->query->id);

	$results = ORM::for_table('title')->find_one(Flight::request()->query->id);
	$results->updated = time();
	$results->save();
	Flight::redirect('/title');
});

// title_del ##################################################
Flight::route('/title_del', function(){
	$results = ORM::for_table('title')->find_one(Flight::request()->query->id);
	$results->delete();
	Flight::redirect('/title');
});
// title_list ##################################################
Flight::route('/title', function(){
// titleがあれば検索
$title = Flight::request()->query->title;
$text = Flight::request()->query->text;
if(!empty($title)){
	//単純検索title
	$results = ORM::for_table('title')->where_like('title',"%$title%");
	$results = $results->order_by_desc('updated');
	$test = "";
}elseif(!empty($text)){
	//単純検索text
	$results2 = ORM::for_table('thread')->where_like('text',"%$text%")->select('title_id')->distinct()->find_many();
	//$results2 = ORM::for_table('thread')->find_one();
	//$test = $results2;
	$results = ORM::for_table('title')->order_by_desc('updated');
	$test = "";
	$title2 = "";
	foreach($results2 as $result2){
		$title2 = ORM::for_table('title')->where('id',$result2->title_id)->find_one()->title;
		$test .= "<a href='thread?id=" . $result2->title_id . "'>" . $title2 . "</a>";
		$test .= "<br>";
	}
	/*
	*/
	//$results = $results->order_by_desc('updated');
}else{
	$results = ORM::for_table('title')->order_by_desc('updated');
	$test = "";
}
	// ページング

	//if(empty($text)){//テキスト検索しないときだけ

	if(isset(Flight::request()->query->page)){
		$page = Flight::request()->query->page;
	}else{
		$page = 1;
	}

	$records = ORM::for_table('title')->count();
	$per_page = 15;
	$offset = ($page - 1) * $per_page;

	$results = $results->offset($offset)->limit($per_page)->find_many();
	//$results = $results->find_many();

	$paging = "";
	//$paging .= $page;
	if(intval($page) > 1){
		$paging .= "<a href='title?page=" . ($page - 1) . "' class='button'>previous</a>  ";
	}
		$paging .= "<a href='title?page=" . ($page + 1) . "' class='button'>next</a>";
	//}//テキスト検索しないときだけ
	// ページングここまで

	$str = "";

	foreach($results as $result){
		$str .= "<tr>";
		$str .= "<td>";
		$str .= $result->id;
		$str .= "</td><td>";
		//$str .= $result->date;
		//$str .= "</td><td>";
		//$str .= $result->title;
		$str .= "<a href='thread?id=" . $result->id . "'>" . $result->title . "</a>";
		$str .= "</td><td>";
		//$str .= nl2br(htmlspecialchars($result->text),false);
		//$str .= "</td><td>";
		//$str .= $result->updated;
		//$str .= "</td><td>";

		$str .= "<a href='title_up?id=" . $result->id . "' class='button'>up</a>";
		//$str .= "</td><td>";
		//$str .= "<a href='title_upd?id=" . $result->id . "' class='button'>update</a>";
		//$str .= "</td><td>";
		//$str .= "<a href='title_del?id=" . $result->id . "'>delete</a>";
		$str .= "</td>";

		$str .= "</tr>";
	}

	Flight::render('header', array('heading' => 'Hello'), 'header_content');
	//Flight::render('title_body', array('str' => $str,'paging' => $paging), 'body_content');
	Flight::render('title_body', array('str' => $str,'paging' => $paging,'test' => $test), 'body_content');
	Flight::render('layout', array('title' => '190106'));

});

// thread ##################################################
Flight::route('/thread', function(){
	$id = Flight::request()->query->id;
	$results = ORM::for_table('title')->find_one($id);

	$str = "";
	$str .= "<a href='title' class='button'>return To Title</a><br>";

	$str .= $results->date;
	$str .= "<br>";
	$str .= $results->title;
	$str .= "<br>";
	$str .= nl2br(htmlspecialchars($results->text),false);

	$str .=<<<EOD
	<!--
	<div class='row'>
		<div class="three columns">
	-->
		<br>
		<a href='thread_title_upd?id=
EOD;
	$str .= $id;
	$str .=<<<EOD
'>update</a><br>

			<form action='' method='get'>
				<input type='hidden' name='id' value="
EOD;
	$str .= $id;
	$str .=<<<EOD
">
				<input type='text' name='text'>
				<input type='submit' value='search'>
			</form>

		<form action='thread_ins_exe' method='post'>
		<input type='hidden' name='id' value="
EOD;
	$str .= $id;
	$str .=<<<EOD
">
			<textarea name='text' style="width:400px;height:150px;"></textarea><br>
			<input type='submit' value='send'>
		</form>
EOD;
	$results = ORM::for_table('thread')->where('title_id',$id);
	$text = Flight::request()->query->text;
	if(!empty($text)){
	//単純検索text
		$results = $results->where_like('text',"%$text%");
		$results = $results->order_by_desc('updated')->find_many();
	}else{
		$results = ORM::for_table('thread')->where('title_id',$id)->order_by_desc('updated')->find_many();
	}

	foreach($results as $result){
		$str .= "<hr>";
		$str .= $result->id;
		$str .= ":";
		$str .= $result->date;
		$str .= "<br>";

                // 対象文字列
		$text = htmlspecialchars($result->text);
                $text = nl2br($text,false);
                // パターン
                $pattern = '/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/u';
                // 置換後の文字列
                $replacement = '<a href="\1">\1</a>';
                // 置換
                $str .= preg_replace($pattern,$replacement,$text);

		$str .= "<br>";
		$str .= "<a href='thread_upd?id=" . $id . "&thread_id=" . $result->id . "'>update</a>";
		$str .= ":";
		$str .= "<a href='thread_del?id=" . $id . "&thread_id=" . $result->id . "'>delete</a>";
		$str .= ":";
		$str .= "<a href='thread_up?id=" . $id . "&thread_id=" . $result->id . "'>up</a>";
		$str .= "<br>";
	}	

	Flight::render('header', array('heading' => 'Hello'), 'header_content');
	Flight::render('body', array('str' => $str), 'body_content');
	Flight::render('layout', array('title' => '190106'));
});

// thread_ins_exe ##################################################
Flight::route('/thread_ins_exe', function(){
	$id = Flight::request()->data->id;
	$result = ORM::for_table('thread')->create();
	$result->date = date('Y-m-d');
	$result->title_id = $id;
	$result->text = Flight::request()->data->text;
	$result->updated = time();
	$result->save();
	Flight::redirect('/thread?id=' . $id);
});

// thread_upd ##################################################
Flight::route('/thread_upd', function(){
	$results = ORM::for_table('thread')->find_one(Flight::request()->query->thread_id);
	$str = "";
	$str .= "<form action='thread_upd_exe' method='post'>";
	$str .= "<input type='hidden' name='id' value=" . Flight::request()->query->id . ">";
	$str .= "<input type='hidden' name='thread_id' value=" . Flight::request()->query->thread_id . ">";

	$str .= "<input type='text' name='date' value='";
	$str .= $results->date;
	$str .= "'><br>";

	$str .= "<textarea name='text' style='width:400px;height:150px;'>";
	//$str .= nl2br(htmlspecialchars($results->text),false);
	//$str .= nl2br($results->text,false);
	$str .= $results->text;
	$str .= "</textarea><br>";

	$str .= "<input type='submit' value='send'>";
	$str .= "</form>";

	Flight::render('header', array('heading' => 'Hello'), 'header_content');
	Flight::render('body', array('str' => $str), 'body_content');
	Flight::render('layout', array('title' => 'todo'));
});

// thread_title_upd ##################################################
Flight::route('/thread_title_upd', function(){
	$results = ORM::for_table('title')->find_one(Flight::request()->query->id);
	$str = "";
	$str .= "<form action='thread_title_upd_exe' method='post'>";
	$str .= "<input type='hidden' name='id' value=" . Flight::request()->query->id . ">";

	$str .= "<input type='text' name='date' value='";
	$str .= $results->date;
	$str .= "'><br>";

	$str .= "<input type='text' name='title' value='";
	$str .= $results->title;
	$str .= "'><br>";

	$str .= "<textarea name='text'>";
	//$str .= nl2br(htmlspecialchars($results->text),false);
	//$str .= nl2br($results->text,false);
	$str .= $results->text;
	$str .= "</textarea><br>";

	$str .= "<input type='submit' value='send'>";
	$str .= "</form>";

	Flight::render('header', array('heading' => 'Hello'), 'header_content');
	Flight::render('body', array('str' => $str), 'body_content');
	Flight::render('layout', array('title' => 'todo'));
});

// thread_title_upd_exe ##################################################
Flight::route('/thread_title_upd_exe', function(){
	$results = ORM::for_table('title')->find_one(Flight::request()->data->id);
	$date = Flight::request()->data->date;
	$title = Flight::request()->data->title;
	$results->date = $date;
	$results->title = $title;
	$results->text = Flight::request()->data->text;
	$results->save();
	Flight::redirect('/thread?id=' . Flight::request()->data->id);
});
// thread_upd_exe ##################################################
Flight::route('/thread_upd_exe', function(){
	$results = ORM::for_table('thread')->find_one(Flight::request()->data->thread_id);
	$results->date = Flight::request()->data->date;
	$results->text = Flight::request()->data->text;
	$title_id = $results->title_id;
	$results->save();

	//titleをup
	$results = ORM::for_table('title')->find_one($title_id);
	$results->updated = time();
	$results->save();

	Flight::redirect('/thread?id=' . Flight::request()->data->id);
});

// thread_del ##################################################
Flight::route('/thread_del', function(){
	$results = ORM::for_table('thread')->find_one(Flight::request()->query->thread_id);
	$results->delete();
	Flight::redirect('/thread?id=' . Flight::request()->query->id);
});

// thread_up ##################################################
Flight::route('/thread_up', function(){

	$results = ORM::for_table('thread')->find_one(Flight::request()->query->thread_id);
	$results->updated = time();
	$results->save();
	Flight::redirect('/thread?id=' . Flight::request()->query->id);
});

Flight::route('/up', function(){
	//$results = ORM::for_table('test')->find_one(Flight::request()->query->id);

	$title = Flight::request()->query->title;
	$cat = Flight::request()->query->cat;

	$results = ORM::for_table('test');

	if($title){
		$results = $results->where_like('title',"%$title%");
	}

	if($cat){
		$results = $results->where_like('cat',"%$cat%");
	}

	$results = $results->order_by_desc('updated')->find_many();

	$myId_i = 0;
	$i = 0;
	foreach($results as $result){
		$ids[] = $result->id;
		// 配列の何番目か調べる
		if(Flight::request()->query->id == $result->id){
			$myId_i = $i;
		}
		$i++;
	}

	// 1番目のレコードでなければ
	if($myId_i != 0){
		$results[$myId_i]->updated = ORM::for_table('test')->find_one($ids[($myId_i - 1)])->updated + 1;
	}

	$results->save();
	Flight::redirect('?title=' . $title . '&cat=' . $cat);
});

// list ##################################################
Flight::route('/*', function(){
	//echo "<a href='index.php?func=ins'>insert</a><br>";
	$str = "";
	//$str .= "<a href='ins'>insert</a><br>";

	/*
	*/

	// クエリ
	$title = Flight::request()->query->title;
	$cat = Flight::request()->query->cat;
	$q_all = Flight::request()->query->q_all;

	$results = ORM::for_table('test');
	// titleがあれば検索
	if(!empty($title)){
		//単純検索title
		$results = $results->where_like('title',"%$title%");
		$cats = $results->distinct()->select('cat')->find_many();
		//$cats = $results->find_many();
	}

	// catがあれば検索
	if(!empty($cat)){
		//単純検索cat
		$results = $results->where_like('cat',"%$cat%");
	}

	if(!empty($q_all)){
	//all検索
	$results = $results->where_raw('("title" like ? or "cat" like ? or "text" like ?)',array("%$q_all%","%$q_all%","%$q_all%"));
	}

	$results = $results->order_by_desc('updated')->find_many();

	$str .=<<<EOD
	<div class='row'>
		<div class="three columns">
		<form action='ins_exe' method='get'>
		<input type='text' name='title' value='
EOD;
		// titleがあれば表示
		if(!empty($title)){
			$str .= $title;
		}
					
	$str .=<<<EOD
		'>
		<input type='text' name='cat' value='
EOD;
		// catがあれば表示
		if(!empty($cat)){
			$str .= $cat;
		}

	$str .=<<<EOD
'><br>
					<textarea name='text' style="width:400px;height:150px;"></textarea>
					<br>
					<input type='submit' value='send'>
		</form>
		</div>

		<div class="three columns">
			<form action='' method='get'>
				<input type='text' name='title'>
				<input type='submit' value='title'>
			</form>
			<form action='' method='get'>
				<input type='hidden' name='title' value='
EOD;
	$str .= $title;
	$str .= "'>";
	$str .=<<<EOD
				<input type='text' name='cat'>
				<input type='submit' value='cat'>
			</form>
			<!--
			-->
			<a href='index.php'>clear</a>
			<ul>
EOD;

	if(!empty($title)){
		foreach($cats as $category){
			$str .= "<li>";
			$str .= $category->cat;
			$str .= "</li>";
		}
	}
/*
*/

	$str .=<<<EOD
			</ul>
		</div>
		<div class="three columns">
			<form action='' method='get'>
				<input type='text' name='q_all'>
				<input type='submit' value='all'>
			</form>
		</div>
	</div>
	<table class="center">
		<thead>
			<tr>
				<th>id</th>
				<!--
				<th>date</th>
				-->
				<th>title</th>
				<th>cat</th>
				<th>text</th>
				<th>updated</th>
				<th>up</th>
				<th>down</th>
				<th>update</th>
				<th>delete</th>
			</tr>
		</thead>
		<tbody>
EOD;
	foreach($results as $result){
		$str .= "<tr>";
		$str .= "<td>";
		$str .= $result->id;
		$str .= "</td><td>";
		//$str .= $result->date;
		//$str .= "</td><td>";
		$str .= $result->title;
		$str .= "</td><td>";
		$str .= $result->cat;
		$str .= "</td><td>";
		$str .= nl2br($result->text,false);
		$str .= "</td><td>";
		$str .= $result->updated;
		$str .= "</td><td>";

		/*
		// 対象文字列
		$text = nl2br($result->text,false);
		// パターン
		$pattern = '/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/u';
		// 置換後の文字列
		$replacement = '<a href="\1">\1</a>';
		// 置換
		$str .= preg_replace($pattern,$replacement,$text);

		$str .= "</td><td>";
		//$str .= $result->updated;
		//$str .= "</td><td>";
		//$str .= $result->archive;
		//$str .= "</td><td>";
		*/
		$str .= "<a href='up?id=" . $result->id . "&title=" . $title . "&cat=" . $cat . "'>up</a>";
		$str .= "</td><td>";
		$str .= "<a href='down?id=" . $result->id . "&title=" . $title . "&cat=" . $cat . "'>down</a>";
		$str .= "</td><td>";
		/*
		if(isset(Flight::request()->query->page)){
			$str .= "<a href='upd?id=" . $result->id . "&page=" . Flight::request()->query->page . "'>update</a>";
		}else{
		}
		*/
		$str .= "<a href='upd?id=" . $result->id . "'>update</a>";
		$str .= "</td><td>";
		$str .= "<a href='del?id=" . $result->id . "'>delete</a>";
		$str .= "</td>";
		$str .= "</tr>";
	}

	$str .=<<<EOD
	</tbody>
	</table>
EOD;

	/*
	// ページング
	if($page > 1){
		$str .= "<a class='button' href='?page=" . ($page - 1) . "'>previous</a>";
	}
	if($page < ceil($records/$per_page)){
		$str .= "<a class='button' href='?page=" . ($page + 1) . "'>after</a>";
	}
	*/
	//echo $str;
	//Flight::render('result.php', array('str' => $str));


	Flight::render('header', array('heading' => 'Hello'), 'header_content');
	Flight::render('body', array('str' => $str), 'body_content');
	Flight::render('layout', array('title' => 'todo'));

//}
});
//if(isset($_GET['func'])){//仮
//if($_GET['func'] == "upd"){//仮
Flight::start();
