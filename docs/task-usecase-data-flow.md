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
    Domain["Domain Rule\n(TaskStatus/TaskTransition/TaskDeadline)"]
    Model["Eloquent Model\n(App\\Models\\Task)"]
    Output["Output DTO\n(TaskListOutput/TaskCommandOutput)"]
    Response["Redirect / View"]

    Client --> Route --> Request --> Controller
    Controller --> Input --> UseCase
    UseCase --> Domain
    UseCase --> Model
    UseCase --> Output
    Output --> Controller --> Response --> Client
```

---

## 2. 一覧（GET `/`）

```mermaid
sequenceDiagram
    actor U as User
    participant C as TaskController@index
    participant LU as ListTasksUseCase
    participant T as Task(Model)
    participant V as tasks.index(view)

    U->>C: GET /
    C->>LU: handle()
    LU->>T: getTaskOrderByDueDate()
    T-->>LU: Collection<Task>
    LU-->>C: TaskListOutput
    C->>V: tasks, success_message を渡す
    V-->>U: HTML
```

---

## 3. 作成（POST `/tasks`）

```mermaid
sequenceDiagram
    actor U as User
    participant R as StoreTaskRequest
    participant C as TaskController@store
    participant D as CreateTaskInput
    participant CU as CreateTaskUseCase
    participant T as Task(Model)

    U->>R: POST /tasks
    R-->>C: validated(title, detail, due_date)
    C->>D: Input DTO生成
    C->>CU: handle(input)
    CU->>T: create(title, detail, due_date, status=not_started)
    CU-->>C: TaskCommandOutput(success)
    C-->>U: redirect(tasks.index) + flash success
```

---

## 4. ステータス更新（POST `/tasks/{task_uuid}/status`）

```mermaid
sequenceDiagram
    actor U as User
    participant R as UpdateTaskStatusRequest
    participant C as TaskController@updateStatus
    participant D as UpdateTaskStatusInput
    participant UU as UpdateTaskStatusUseCase
    participant TT as TaskTransition(Domain)
    participant T as Task(Model)

    U->>R: POST /tasks/{task_uuid}/status
    R-->>C: validated(task_uuid, status)
    C->>D: Input DTO生成
    C->>UU: handle(input)
    UU->>T: 現在タスク取得(task_uuid)
    UU->>TT: canTransition(current_status, next_status)
    alt 遷移可
        UU->>T: updateStatusByUuid(task_uuid, status)
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
    participant R as DestroyTaskRequest
    participant C as TaskController@destroy
    participant D as DeleteTaskInput
    participant DU as DeleteTaskUseCase
    participant T as Task(Model)

    U->>R: DELETE /tasks/{task_uuid}
    R-->>C: validated(task_uuid)
    C->>D: Input DTO生成
    C->>DU: handle(input)
    DU->>T: deleteByUuid(task_uuid)
    alt 削除成功
        DU-->>C: TaskCommandOutput(success)
        C-->>U: redirect + flash success
    else 対象なし
        DU-->>C: TaskNotFoundException
        C-->>U: redirect + flash error
    end
```

