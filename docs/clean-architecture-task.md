# Clean Architecture 導入方針（Task機能）

本ドキュメントは、Task機能を対象とした **Domain / Application / Infrastructure / Interface** の責務・依存方向・命名/配置ルール・移行順序を定め、以降の実装タスクで設計判断に迷わないようにするための方針である。

---

## 1. レイヤー構成と責務

### 1.1 Domain（ドメイン層）

**責務**

- ビジネスルールとエンティティの定義
- フレームワーク・DB・UIに依存しない純粋なビジネスロジック
- タスクの状態・値オブジェクト・ドメインルールの表現

**含めるもの**

- エンティティ（Task の識別子・属性・不変条件）
- 値オブジェクト（例: ステータス、期限日などドメイン上の意味を持つ値）
- ドメインの定数・列挙（例: ステータス値: not_started / in_progress / completed）
- ドメインサービス（複数エンティティにまたがる純粋なルール）

**含めないもの**

- 永続化（Eloquent・Repository実装）
- HTTP・リクエスト・レスポンス
- フレームワーク固有のクラスへの依存

---

### 1.2 Application（アプリケーション層 / Use Case）

**責務**

- ユースケースのオーケストレーション
- 入力の受け取り・ドメイン/インフラの呼び出し・出力の返却
- トランザクション境界の定義（「1ユースケース = 1トランザクション」とする場合の開始/コミットはここで扱う）

**含めるもの**

- ユースケースクラス（例: CreateTaskUseCase, UpdateTaskStatusUseCase, DeleteTaskUseCase, ListTasksUseCase）
- 入力DTO（Use Case への引数をまとめたオブジェクト）
- UseCase の公開シグネチャ（戻り値）は **ドメインエンティティではなく専用の出力 DTO/OutputModel** とする。UseCase 内でドメイン操作後にドメイン→出力 DTO へ変換するマッパー（例: DomainToOutputMapper）を用い、Interface 層（Controller/Presenter/ViewModel）はこの出力 DTO を受け取り、表示用にさらに変換するかそのまま返却する。これによりドメインの内部構造が Interface 層に露出しない。
- Application 層に定義したリポジトリ**インターフェース**（Port）の利用

**含めないもの**

- HTTP の Request/Response の直接参照（Interface層で変換してから渡す）
- Eloquent Model や DB の直接参照（Repository インターフェース経由のみ）
- ビジネスルールの詳細（Domain に委譲）

---

### 1.3 Infrastructure（インフラストラクチャ層）

**責務**

- 永続化・外部サービス・フレームワークとの接続の実装
- Application 層が定義した Port（インターフェース）の Adapter 実装

**含めるもの**

- リポジトリの実装（例: EloquentTaskRepository）
- Eloquent Model（DB マッピング用。Domain のエンティティとは分離し、必要に応じて変換）
- 外部APIクライアント、ファイルシステムアクセス等の実装

**含めないもの**

- ビジネスルール
- HTTP ルーティング・コントローラの振る舞いの決定（「何をするか」は Application、「どう届けるか」は Interface）

---

### 1.4 Interface（インターフェース層 / プレゼンテーション）

**責務**

- 入出力の受け取りと Application 層への橋渡し
- HTTP リクエストのバリデーション、レスポンス形式の決定、エラーハンドリング

**含めるもの**

- HTTP Controller（ルーティングから呼ばれる入口）
- FormRequest（バリデーション・認可）
- リクエスト → Use Case 入力DTO の変換
- Use Case の戻り値 → View/JSON への変換
- Blade 等の View（表示のみ。複雑な表示ロジックは ViewModel や Presenter に分離するかは別Issueで検討）

**含めないもの**

- ユースケースの手順（Application に記述）
- 永続化の実装（Infrastructure に記述）
- ドメインルール（Domain に記述）

---

## 2. 依存の方向

- **依存は内側に向かう**
  - Interface → Application → Domain
  - Infrastructure → Application（Port の実装）→ Domain
- **Domain は誰にも依存しない**（他レイヤー・フレームワーク・DB に依存しない）
- **Application は Domain にのみ依存し、Infrastructure の実体には依存しない**
  - Application は「リポジトリのインターフェース（Port）」に依存する
  - 実装（Adapter）は Infrastructure に配置し、Laravel の DI で注入する

```text
[Interface]  →  [Application]  →  [Domain]
     ↑                ↑
     |                +---- (Port/Interface のみ参照)
     |
[Infrastructure] ────┘
     (Adapter が Port を実装)
```

---

## 3. 命名・配置ルール

### 3.1 ディレクトリ・名前空間

| レイヤー     | 推奨パス（app 以下）                    | 名前空間例                |
|-------------|------------------------------------------|---------------------------|
| Domain      | `app/Domain/Task/`                       | `App\Domain\Task`         |
| Application | `app/Application/Task/`                  | `App\Application\Task`    |
| Infrastructure | `app/Infrastructure/Persistence/Task/` | `App\Infrastructure\Persistence\Task` |
| Interface   | `app/Http/Controllers/` 等（既存）      | `App\Http\Controllers`    |

- Domain: サブドメインや集約単位でディレクトリを切る（本プロジェクトでは `Task` を単位とする）
- Application: 機能（Task）ごとに UseCase と Port をまとめる
- Infrastructure: Persistence（DB）、External（API）など関心で分割可能
- Interface: Laravel 標準の `Http/Controllers`、`Http/Requests` を利用し、必要なら `app/Http/Controllers/Task/` のように機能でサブディレクトリを切る

### 3.2 クラス命名

| 種類               | 命名例                          |
|--------------------|---------------------------------|
| ドメインエンティティ | `TaskEntity`（Domain 用。Eloquent Model との混同を避けるため、ドメインエンティティは必ず `〜Entity` サフィックスを付ける） |
| 値オブジェクト       | `TaskStatus`, `DueDate` 等      |
| ユースケース         | `CreateTaskUseCase`, `ListTasksUseCase` |
| リポジトリ Port     | `TaskRepositoryInterface`（Application 層に interface を配置） |
| リポジトリ Adapter  | `EloquentTaskRepository`（Infrastructure の実装） |
| Controller         | `TaskController`（既存のまま）  |
| FormRequest        | `StoreTaskRequest` 等（既存のまま） |

- ドメインエンティティは **必ず `〜Entity` サフィックス** を付ける（例: `TaskEntity`）。理由: Eloquent Model（例: `App\Models\Task`）と名前が衝突しないようにするため。Domain 層では `Task` ではなく `TaskEntity` を一貫して用いる。
- UseCase は「動詞 + 対象 + UseCase」とする
- Port は「〜RepositoryInterface」とし、Application 側に interface を置く（実装は「Eloquent〜Repository」等の Adapter 名で Infrastructure に配置）
- Adapter は「実装手段 + Port名」とする（例: EloquentTaskRepository）

### 3.3 ファイル配置例（Task 機能）

```text
app/
├── Domain/
│   └── Task/
│       ├── TaskEntity.php        # ドメインエンティティ
│       ├── TaskStatus.php        # 値オブジェクト or 定数クラス
│       └── ...
├── Application/
│   └── Task/
│       ├── CreateTaskUseCase.php
│       ├── ListTasksUseCase.php
│       ├── UpdateTaskStatusUseCase.php
│       ├── DeleteTaskUseCase.php
│       └── TaskRepositoryInterface.php   # interface (Port)
├── Infrastructure/
│   └── Persistence/
│       └── Task/
│           ├── EloquentTaskRepository.php
│           └── EloquentTaskModel.php   # Eloquent Model（DB用。必要なら Domain Task と分離）
├── Http/
│   ├── Controllers/
│   │   └── TaskController.php
│   └── Requests/
│       ├── StoreTaskRequest.php
│       └── ...
```

- 既存の `App\Models\Task` は、移行期間中は Eloquent Model として Infrastructure に相当する役割を持たせ、段階的に `Domain\Task` と `Infrastructure\Persistence\Task\EloquentTaskModel` に分離するかどうかは移行タスクで判断する

---

## 4. 移行順序

既存コードは「Controller → Model（Eloquent）に処理が集中」しているため、以下の順でレイヤーを導入し、責務を分離する。

1. **Domain の切り出し**
   - ステータス定数・エンティティに必要な属性・値オブジェクトを `app/Domain/Task/` に定義
   - 既存 `App\Models\Task` の定数（STATUS_*）は Domain に移し、Model は Domain を参照するか、一時的に定数を残して後で削除

2. **Application の Port と UseCase の追加**
   - `TaskRepositoryInterface` を Application に定義
   - Create / List / UpdateStatus / Delete の各 UseCase を追加
   - UseCase は Domain のエンティティと Port にのみ依存するようにする

3. **Infrastructure の実装**
   - `TaskRepositoryInterface` を実装する `EloquentTaskRepository` を追加
   - 既存の `App\Models\Task` をリポジトリ内で利用する形にし、必要なら `EloquentTaskModel` として Infrastructure に移動・リネーム

4. **Interface の差し替え**
   - `TaskController` から直接 Model を触らないようにする
   - Controller は FormRequest で受け取り → 入力DTO に変換 → UseCase 実行 → 戻り値を View 用に変換
   - 既存の FormRequest はそのまま利用可能（入力の形が UseCase の入力DTO と一致するようにする）

5. **テストの移行**
   - Domain: 単体テスト（ルール・エンティティの振る舞い）
   - Application: UseCase の単体テスト（Repository をモック）
   - Infrastructure: リポジトリの統合テスト（DB 使用可）
   - Interface: 既存の Browser/Feature テストを維持しつつ、Controller の責務が薄いことを確認

この順序で進めることで、既存の Task 機能を壊さずに、段階的に Clean Architecture に寄せられる。

---

## 5. 本ドキュメントの使い方

- Task 関連の実装は、上記の責務・依存方向・命名・配置・移行順序に従って進める
- 例外や詳細で迷った場合は、先に本ドキュメントを更新して判断基準を明文化してから実装する
- 既存の `app/Models/Task` および `app/Http/*` のリファクタリングは、上記移行順序に従って段階的に進める

---

## 6. 本ドキュメントの整備完了条件

本ドキュメントは、以下を満たした時点で「整備完了」とする。

- Task機能に対するレイヤー責務（Domain / Application / Infrastructure / Interface）が文書化されている
- 依存方向（内側依存）と DI 方針（Port/Adapter）が図と文章で定義されている
- 命名規約・配置規約が、少なくとも Task 機能で迷わず適用できる粒度で記載されている
- 移行順序（Domain → Application → Infrastructure → Interface → Test）が定義され、次タスクに分割可能な状態になっている
- レビューで内容が合意され、Task 実装の判断基準として利用可能である

---

## 7. 本ドキュメントの記載対象外

本ドキュメントは設計方針の定義を目的とするため、以下は記載対象外とする。

- 実コードの全面移行（Controller/Model/Repositoryの置換実装）
- 既存テストの全面書き換え
- DBスキーマ変更・マイグレーション追加
- Task機能以外（User等）への横展開

---

## 8. Task機能の移行完了判定基準

Task機能が Clean Architecture へ移行完了したとみなす基準を以下に定義する。

- Controller が Eloquent Model を直接呼び出さず、UseCase 経由で処理を実行している
- UseCase が Repository の interface（Port: TaskRepositoryInterface）にのみ依存している
- Repository 実装（Eloquent）は Infrastructure に閉じている
- Domain 層にフレームワーク依存（Illuminate系）が混入していない
- 既存の主要機能（作成/一覧/状態更新/削除）が退行なく動作し、既存テストまたは同等テストで担保されている

---

## Related

- Issue: [Clean Architecture導入方針の策定 #27](https://github.com/pasgroup/laravel-playground/issues/27)
- Doc: [Task機能 データフロー図（UseCase版）](./task-usecase-data-flow.md)
