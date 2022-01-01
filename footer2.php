<footer id="footer">
	Copyright <a href="http://webukatu.com">ウェブカツ！！WEBサービス部</a>. All Rights Reserved.
</footer>

<script src="js/vendor/jquery-2.2.2.min.js"></script>
<script>
	$(function(){
		//フッターを最下部に固定
		var $ftr = $('#footer');
		if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
			$ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;' });
		}

		//メッセージ表示
		//jqueryでDOMを取得（area-drop内）
		var $jsShowMsg = $('#js-show-msg');
		var msg = $jsShowMsg.text();
		//空白じゃなくて文字があるか判定
		if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
			$jsShowMsg.slideToggle('slow');
			setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 5000);
		}

		//画像ライブプレビュー
		//jqueryでDOMを取得（area-drop内）
		var $dropArea = $('.area-drop');
		var $fileInput = $('.input-file');
		$dropArea.on('dragover', function(e){
			e.stopPropagation();
			e.preventDefault();
			//this->dropArea
			$(this).css('border', '3px #ccc dashed');
		});
		//要約：画像を離したとき線を消すよ
		$dropArea.on('dragleave', function(e){
			e.stopPropagation();
			e.preventDefault();
			$(this).css('border', 'none');
		});
		//inputの中身が変更された場合
		$fileInput.on('change', function(e){
			$dropArea.css('border', 'none');
			//配列の一番最初（キー）に０を入れる
			//this->$fileInputでドラッグ&ドロップしたもの
			var file = this.files[0],             //2.files配列にファイルが入っています。
				$img = $(this).siblings('.prev-img'), //3.jqueryのsiblingsメソッドで兄弟のimgを取得
				fileReader = new FileReader();		  //4.ファイルを読み込むFileReaderオブジェクトを生成

			//5.読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
			fileReader.onload = function(event) {
				//読み込んだデータをimgに設定
				$img.attr('src', event.target.result).show();
			};

			//6.画像読み込み（file->配列から取り出したもの）
			fileReader.readAsDataURL(file);
		});

		//テキストエリアカウント
		var $countUp = $('#js-count'),
			$countView = $('#js-count-view');
		$countUp.on('keyup', function(e){
			$countView.html($(this).val().length);
		});

		//画像切替
		var $switchImgSubs = $('.js-switch-img-sub'),
        	$switchImgMain = $('#js-switch-img-main');
    $switchImgSubs.on('click',function(e){
      $switchImgMain.attr('src',$(this).attr('src'));
		});

		//お気に入り登録・削除
		//$...DOMを取得するため
		var $like,
			likeProductId;
		$like = $('.js-click-like') || null; //nullというのはnull値という値で、「変数の中身は空ですよ」と明示するために使う値
		likeProductId = $like.data('productid') || null;
		//数値の０はfalseと判定されてしまう。product_idが０の場合もあり得るので、０もtrueとする場合にはunderfinedとnullを判定する
		if(likeProductId !== undefined && likeProductId !== null){
			$like.on('click',function(){
				//$this...$like...アイコンのDOM
				var $this = $(this);
				$.ajax({
					type: "POST",
					url: "ajaxLike.php",
					//キーがproductId値がlikeProductId
					data: { productId : likeProductId}
				}).done(function( data ){
					console.log('Ajax Success');
					//クラス属性をtoggleで付け外しする
					$this.toggleClass('active');
				}).fail(function( msg ) {
					console.log('Ajax Error');
				});
			});
		}

	});

</script>
</body>
	</html>
	$sql = 'SELECT id FROM cats';

			if(!empty($gender)){
				$sql .= ' WHERE gender_id = '.$gender;
			if(!empty($prefecture)){
				$sql .= ' AND prefecture_id = '.$prefecture;
			if(!empty($age)){
				 $sql .= ' AND age_id = '.$age;
			}
		}
		}else{
			if(!empty($prefecture)){
				$sql .= ' WHERE prefecture_id = '.$prefecture;
			if(!empty($age)){
				 $sql .= ' AND age_id = '.$age;
			}
		}
	
		}