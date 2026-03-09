# Interface 詳細（Task）

## 対象ディレクトリ

- `app/Http/Controllers/`
- `app/Http/Requests/`
- `app/Http/Presenters/`
- `resources/views/tasks/`

## 何を置くか

- Controller: 入力受付、UseCase呼び出し、例外ハンドリング
- FormRequest: バリデーションと認可
- Presenter / Formatter: 表示専用データへの変換
- ViewModel / Blade: 描画専用

## 依存ルール

- Controller は UseCase を呼ぶ（Model直参照しない）
- Presenter は Application の出力DTOを受け、ViewModelへ整形する
- Blade は ViewModel の値を描画するだけにする
