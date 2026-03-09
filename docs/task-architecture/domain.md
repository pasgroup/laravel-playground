# Domain 詳細（Task）

## 対象ディレクトリ

- `app/Domain/Task/Entity/`
- `app/Domain/Task/ValueObject/`
- `app/Domain/Task/Service/`
- `app/Domain/Task/Policy/`
- `app/Domain/Task/Specification/`

## 何を置くか

- `Entity`: 識別子と状態を持つドメインオブジェクト（例: `TaskEntity`）
- `ValueObject`: ドメイン意味を持つ値（例: `TaskStatus`）
- `Service`: 複数オブジェクトにまたがる判断/生成ロジック（例: `TaskCreationService`, `TaskStatusDecisionService`）
- `Policy`: 遷移可否などのルール定義（例: `TaskTransition`, `TaskTransitionPolicyInterface`）
- `Specification`: 条件判定（例: `TaskDeadline`）

## 何を置かないか

- Eloquent/DBアクセス
- HTTPリクエスト/レスポンス
- Laravel固有のフレームワーク依存

## 命名ルール

- エンティティは `*Entity`
- 値オブジェクトは業務語彙を優先（`TaskStatus`）
- サービスは `*Service`
- ポリシーは `*PolicyInterface` / 実装クラス
