# Infrastructure 詳細（Task）

## 対象ディレクトリ

- `app/Infrastructure/Persistence/Task/`
- `app/Models/Task.php`（Eloquent Model）

## 何を置くか

- `TaskRepositoryInterface` の実装（`EloquentTaskRepository`）
- Eloquentを使った取得/保存/更新/削除
- Eloquent Model <-> Domain Entity のマッピング

## 何を置かないか

- ユースケースの手順
- ドメインルールの定義
- HTTP入出力処理

## 実装上の注意

- UseCase からは `Repository Interface` 経由で呼ばれる
- DIバインドは `AppServiceProvider` で管理する
