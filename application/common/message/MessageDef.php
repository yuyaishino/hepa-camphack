<?php
/**
 * メッセージ定義
 *
 * Date:     2018/03/06
 * Author:   xuweiwei
 *
 **/

function getMessageDefinitionArray()
{
	$MessageDefinitionArray = array(
			//メッセージ定義
			"I0001" => "###を入力してください。",
			"I0002" => "###行目の###を入力してください。",
			"I0003" => "全角で入力ください。",
			"I0004" => "全角カタカナで入力ください。",
			"I0005" => "正しい生年月日をYYYYMMDDで入力してください。",
            "I0006" => "正しい郵便番号を入力してください。",
			"I0007" => "正しい電話番号を入力してください。",
			"I0008" => "パスワードは半角英数の6桁～20桁で入力ください。",
			"I0009" => "パスワードが一致しません。",
			"I0010" => "このメールアドレスはご登録されておりません。",
			"I0011" => "応募できるポイントが不足しています。",
			"I0012" => "何も選ばれていません。",
			"I0013" => "認証コードを入力してください。",
			"I0014" => "一定時間が経過しましたので、ログアウトしました。",
			"I0020" => "全角で入力ください。",
			"I0021" => "全角カタカナで入力ください。",
			"I0022" => "郵便番号から正しい住所を入力してください。",
                        "I0023" => "未操作時間が10分以上経過しました。「戻る」ボタンで前画面に戻り、入力をお願い致します。",
            "I0075" => "###は既に存在しています。",
			"I0090" => "IDまたはパスワードが正しくありません。",
			"I0091" => "当社で設定できるパスワード桁数は6～20桁です。パスワードの桁数をお確かめ願います。",
            "I0092" => "このIDは既に存在します",
            "I0093" => "シリアルNo.①~④すべてが4桁の数字である",
            "I0094" => "登録済みのシリアルNo.です",
            "I0095" => "認証コードすべてが4桁の数字である",
            "I0096" => '認証コードが一致していません。',
			"I0097" => '存在しないシリアルNo.です。',
			"I0098" => '認証コードの不一致が3回となったためログアウトします。',
			'I0099' => 'パスワードは半角英数の6桁～20桁で入力ください。',
            'I0100' => '全角で入力ください。',
            'I0101' => '全角カタカナで入力ください。',
			'I0102' => 'システムエラー',
			'I0103' => 'ID(メールアドレス）をご確認ください。',


			"E0001" => "該当データは他の端末から既に削除されました。",
			"E0003" => "各数量の合計が仕入量を超えています。",
			"E0005" => "該当データは他の端末から既に変更されました。",
			"E0007" => "",
			


			"R0001" => "訂正されていません。",
			"R0002" => "正常に訂正されました。",
			"R0003" => "正常に登録されました。",
			"R0004" => "正常に更新されました。",
			"R0005" => "正常に削除されました。",
			"R0006" => "登録が失敗しました、システム管理者に連絡してください。",
			"R0007" => "編集が失敗しました、システム管理者に連絡してください。",
			"R0008" => "削除が失敗しました、システム管理者に連絡してください。",
			"R0009" => "",
			"R0016" => "訂正理由が未設定です。",
            "R0018" => "手板情報を更新しました。",
            "R0019" => "手板テンプレートを更新しました。",
            "R0020" => "出荷情報を更新しました。",


			"S0001" => "原因不明のエラーです。システム管理者に問い合わせてください。",
			"S0002" => "該当パラメータが無効であるから、システム管理者に問い合わせてください。",
			"S0003" => "DBアクセスエラーです。システム管理者に問い合わせてください。",
			"S0004" => "無効の集計表データの配列です。システム管理者に問い合わせてください。",
			"S0005" => "サーバーでシステムエラーが発生しました。",
			"S0006" => "一覧に無効な商品が存在します。システム管理者に連絡してください。",
			"S0007" => "該当###が###に存在していません、システム管理者に連絡してください。",
            "S0008" => "該当###が存在していません、システム管理者に連絡してください。",
			"S999" => "危険なURLからアクセスです。システム管理者に問い合わせてください。",
			"S998" => "このURLは直接にアクセスできません。画面のボタンをクリックしてアクセスしてください。",
			"S997" => "無効のデータです。システム管理者に問い合わせてください。",

			"I00088" => "指定された条件に該当するデータはありません。",

			"I00114" => "新パスワードと確認パスワードが一致しません。",

			"I00121" => "保存データがありません。",
			

			"C01006" => "ログアウトします。よろしいですか？",
			"E00012" => "該当ユーザーIDは既に存在するため、登録できません。",
			"S00104" => "接続の有効時間が切れました。再ログオンしてください。",
			"R00011" => "指定された条件に該当するデータはありません。",
			"R10008" => "パスワード変更が失敗しました。",
			"R01009" => "該当ユーザー状態が無効であるため、ログインできません。",


    );
    return $MessageDefinitionArray;
}