# Application 詳細（Task）

## 対象ディレクトリ

- `app/Application/Task/UseCase/`
- `app/Application/Task/DTO/`
- `app/Application/Task/Repository/`
- `app/Application/Task/Exceptions/`

## 何を置くか

- `UseCase`: ユースケース実行の手順（例: `CreateTaskUseCase`）
- `DTO`: 入出力データ構造（例: `CreateTaskInput`, `TaskListOutput`）
- `Repository Interface`: 永続化ポート（例: `TaskRepositoryInterface`）
- `Exceptions`: アプリケーション例外（例: `TaskNotFoundException`）

## 依存ルール

- UseCase は `Repository Interface` と Domain に依存する
- UseCase は Infrastructure 実装（Eloquent等）に依存しない
- UseCase は必要箇所のみ Domain Service を呼ぶ
  - 例: 作成で `TaskCreationService`
  - 例: 状態更新で `TaskStatusDecisionService`
