# Interface 詳細（Task）

## 対象ディレクトリ

- `app/Http/Controllers/`
- `app/Http/Requests/`
- `app/Http/Presenters/`
- `resources/views/tasks/`

## 何を置くか

- Controller: 入力受付、UseCase呼び出し、例外ハンドリング
- FormRequest: バリデーションと認可
- Presenter / Formatter: Application出力DTOを表示用または応答用データへ変換
- ViewModel / Blade: 画面描画専用（flash/redirectは Presenter が整形）

## 依存ルール

- Controller は UseCase を呼ぶ（Model直参照しない）
- Presenter は Application の出力DTOを受け、ViewModelまたはflash応答データへ整形する
- Blade は ViewModel の値を描画するだけにする
