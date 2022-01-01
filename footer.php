		<!--footer-->
		<footer id="footer">
			Copyright<a href="">nekonote</a>. All Rights Reserved.
		</footer>
		<script src="js/vendor/jquery-2.2.2.min.js"></script>
		<script>
			$(function(){
				//id='footer'のDOMを取得します。footerの要素を取得
				var $ftr = $('#footer');
				//.innerHeightは要素のpaddingを含んだ高さを取得するメソッド
				//.outerHeightは要素のborder、paddingを含んだ高さを取得するメソッド
				//.offset().topはその要素の上端の位置を取得するメソッド
				if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
					$ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;' });
					//style属性を追加して、まず固定するためにposition: fixed;を指定します。top:〜で固定場所を指定します。
					//window.innerHeight - $footer.outerHeight + 'px;'で表示画面の下端からフッターのheightを差し引いた位置を指定しています。
					//つまり、ページ長さに応じて自動でフッターがページ最下部に固定されるということです。
				}

				//メッセージ表示
				//jQueryでDOMを取得して入れる
				var $jsShowMsg = $('#js-show-msg');
				//取り出したDOM
				var msg = $jsShowMsg.text();
				if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
					$jsShowMsg.slideToggle('slow');
					setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 5000);
				}

				// テキストエリアカウント
				var $countUp = $('#js-count');
				var $countView = $('#js-count-view');
				$countUp.on('keyup',function(e){
					$countView.html($(this).val().length);
				});

				// 画像ライブプレビュー
				// DOMを取ってくる
				var $dropArea = $('.area-drop');
				var $fileInput = $('.input-file');

				$dropArea.on('dragover', function(e){
					// 親要素への伝搬をキャンセルする
					e.stopPropagation();
					// 要素に入っているイベントをキャンセル
					e.preventDefault();
					$(this).css('border', '1px #ccc dashed');
				});

				$dropArea.on('dragleave', function(e){
					e.stopPropagation();
					e.preventDefault();
					$(this).css('border', 'none');
				});

				$fileInput.on('change', function(e){
					$dropArea.css('border', 'none');
					// 2.file配列にファイルが入っています
					var file = this.files[0],
					// 3.jqueryのsiblingsメソッドで兄弟のimg prev-imgを取得
					$img = $(this).siblings('.prev-img'),
					// 4.ファイルを読み込むfileReaderオブジェクト
					fileReader = new FileReader();

					// 5.読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
					fileReader.onload = function(event) {
						// 読み込んだデータをimgに設定
						$img.attr('src', event.target.result).show();
					};
					//6.画像読み込み
					fileReader.readAsDataURL(file);
				});

				// 画像切り替え
				var $switchImgSub = $('.js-switch-img-sub');
				var $switchImgMain = $('#js-switch-img-main');

				$switchImgSub.on('click',function(e){
					$switchImgMain.attr('src',$(this).attr('src'));
				});

				// お気に入り登録・削除

				var $like,
					likecatId;
				$like = $('.js-click-like') || null;
				likecatId = $like.data('catid') || null;

				if(likecatId !== undefined && likecatId !== null){
					$like.on('click',function(){
						var $this = $(this);
						$.ajax({
							type: "POST",
							url: "ajaxLike.php",
							data: { catId : likecatId }
						}).done(function(data){
							console.log('Ajax Success');
							// クラス属性をtoggleで付け外しする
							$this.toggleClass('active');
						}).fail(function(msg){
							console.log('Ajax error');
						});
					});
				}
			});
		</script>
	</body>
</html>