# Task機能 データフロー図（UseCase版）

このドキュメントは、Task機能の現在実装におけるデータの流れを可視化するためのものです。  
GitHub上で確認しやすいように、`mermaid` で記載しています。

---

## 1. 全体レイヤーフロー

```mermaid
flowchart LR
    Client["Browser / HTTP Client"]
    Route["routes/web.php"]
    Request["FormRequest\n(Store/UpdateStatus/Destroy)"]
    Controller["TaskController"]
    Input["Input DTO\n(Create/Update/Delete)"]
    UseCase["UseCase\n(Create/List/Update/Delete)"]
    Domain["Domain\n(Entity/ValueObject/Service/Policy/Specification)"]
    Port["Repository Interface\n(TaskRepositoryInterface)"]
    DI["Service Container\n(AppServiceProvider bind)"]
    Repo["Infrastructure\n(EloquentTaskRepository)"]
    Model["Eloquent Model\n(App\\Models\\Task)"]
    Presenter["Presenter/Formatter\n(TaskIndexPresenter/TaskIndexItemFormatter)"]
    ViewModel["ViewModel\n(TaskIndexViewModel)"]
    View["View\n(resources/views/tasks/index.blade.php)"]
    Output["Output DTO\n(TaskListOutput/TaskCommandOutput)"]
    Response["Redirect / View"]

    Client --> Route --> Request --> Controller
    Controller --> Input --> UseCase
    UseCase --> Domain
    UseCase --> Port
    Port -. resolve .-> DI --> Repo --> Model
    UseCase --> Output
    Output --> Controller
    Controller --> Presenter
    Presenter --> ViewModel
    Controller --> View
    ViewModel --> View
    View --> Response --> Client
```

---

## 2. 一覧（GET `/`）

```mermaid
sequenceDiagram
    actor U as User
    participant C as TaskController@index
    participant LU as ListTasksUseCase
    participant Repo as TaskRepositoryInterface
    participant ER as EloquentTaskRepository
    participant T as Task(Model)
    participant P as TaskIndexPresenter
    participant F as TaskIndexItemFormatter
    participant V as tasks.index(view)

    U->>C: GET /
    C->>LU: handle()
    LU->>Repo: getTaskOrderByDueDate()
    Repo->>ER: 実装呼び出し
    ER->>T: クエリ実行
    T-->>ER: Eloquent records
    ER-->>Repo: list<TaskEntity>
    Repo-->>LU: list<TaskEntity>
    LU-->>C: TaskListOutput
    C->>C: success = session('success')\nerror = session('error')
    C->>P: present(output, success, error)
    P->>F: toViewModel(task, previous_task)
    F-->>P: TaskIndexItemViewModel
    P-->>C: TaskIndexViewModel
    C->>V: view_model を渡す
    V-->>U: HTML
```

---

## 3. 作成（POST `/tasks`）

```mermaid
sequenceDiagram
    actor U as User
    participant Req as StoreTaskRequest
    participant C as TaskController@store
    participant D as CreateTaskInput
    participant CU as CreateTaskUseCase
    participant S as TaskCreationService(Domain)
    participant Repo as TaskRepositoryInterface
    participant ER as EloquentTaskRepository
    participant T as Task(Model)

    U->>Req: POST /tasks
    Req-->>C: validated(title, detail, due_date)
    C->>D: Input DTO生成
    C->>CU: handle(input)
    CU->>S: createForNewTask(...)
    S-->>CU: TaskEntity
    CU->>Repo: create(task_entity)
    Repo->>ER: 実装呼び出し
    ER->>T: create(...)
    T-->>ER: created model
    ER-->>Repo: TaskEntity(task_id含む)
    Repo-->>CU: TaskEntity
    CU-->>C: TaskCommandOutput(success, task_id)
    C-->>U: redirect(tasks.index) + flash success
```

---

## 4. ステータス更新（POST `/tasks/{task_uuid}/status`）

```mermaid
sequenceDiagram
    actor U as User
    participant Req as UpdateTaskStatusRequest
    participant C as TaskController@updateStatus
    participant D as UpdateTaskStatusInput
    participant UU as UpdateTaskStatusUseCase
    participant DS as TaskStatusDecisionService(Domain)
    participant Repo as TaskRepositoryInterface
    participant ER as EloquentTaskRepository
    participant T as Task(Model)

    U->>Req: POST /tasks/{task_uuid}/status
    Req-->>C: validated(task_uuid, status)
    C->>D: Input DTO生成
    C->>UU: handle(input)
    UU->>Repo: findByUuid(task_uuid)
    Repo->>ER: 実装呼び出し
    ER->>T: レコード取得
    T-->>ER: model|null
    ER-->>Repo: TaskEntity|null
    Repo-->>UU: TaskEntity|null
    UU->>DS: resolveTransition(current_status, next_status)
    alt 遷移可
        UU->>Repo: updateTaskStatusByUuidAndCurrentStatus(...)
        Repo->>ER: 実装呼び出し
        ER->>T: 条件付きUPDATE
        T-->>ER: affected_rows
        ER-->>Repo: affected_rows
        Repo-->>UU: affected_rows
        UU-->>C: TaskCommandOutput(success)
        C-->>U: redirect + flash success
    else タスクなし/遷移不可
        UU-->>C: TaskApplicationException
        C-->>U: redirect + flash error
    end
```

---

## 5. 削除（DELETE `/tasks/{task_uuid}`）

```mermaid
sequenceDiagram
    actor U as User
    participant Req as DestroyTaskRequest
    participant C as TaskController@destroy
    participant D as DeleteTaskInput
    participant DU as DeleteTaskUseCase
    participant Repo as TaskRepositoryInterface
    participant ER as EloquentTaskRepository
    participant T as Task(Model)

    U->>Req: DELETE /tasks/{task_uuid}
    Req-->>C: validated(task_uuid)
    C->>D: Input DTO生成
    C->>DU: handle(input)
    DU->>Repo: deleteTaskByUuid(task_uuid)
    Repo->>ER: 実装呼び出し
    ER->>T: DELETE(soft delete)
    T-->>ER: deleted_rows
    ER-->>Repo: deleted_rows>0
    Repo-->>DU: bool
    alt 削除成功
        DU-->>C: TaskCommandOutput(success)
        C-->>U: redirect + flash success
    else 対象なし
        DU-->>C: TaskNotFoundException
        C-->>U: redirect + flash error
    end
```

