# Task機能 データフロー図（master / MVC版）

このドキュメントは、`master` ブランチの Task 機能におけるデータの流れを可視化するためのものです。  

---

## 1. 全体レイヤーフロー

```mermaid
flowchart LR
    Client["Browser / HTTP Client"]
    Route["routes/web.php"]
    Request["FormRequest\n(Store/UpdateStatus/Destroy)"]
    Controller["TaskController"]
    Model["Eloquent Model\n(App\\Models\\Task)"]
    View["View\n(resources/views/tasks/*.blade.php)"]
    Response["Redirect / View"]

    Client --> Route --> Request --> Controller
    Controller --> Model
    Model --> Controller
    Controller --> View
    View --> Response --> Client
```

補足:

- `master` では UseCase / Repository Interface / Presenter / ViewModel は導入していない
- `TaskController` が Model 呼び出しとレスポンス組み立てを直接担う

---

## 2. 一覧（GET `/`）

```mermaid
sequenceDiagram
    actor U as User
    participant C as TaskController@index
    participant T as Task(Model)
    participant V as tasks.index(view)

    U->>C: GET /
    C->>T: getTaskOrderByDueDate()
    T-->>C: Collection<Task>
    C->>V: tasks, success_message を渡す
    V-->>U: HTML
```

---

## 3. 作成（POST `/tasks`）

```mermaid
sequenceDiagram
    actor U as User
    participant Req as StoreTaskRequest
    participant C as TaskController@store
    participant T as Task(Model)

    U->>Req: POST /tasks
    Req-->>C: validated(title, detail, due_date)
    C->>T: createStatusNotStartedTask(...)
    T->>T: create(...)
    T-->>C: Task(Model)
    C-->>U: redirect(tasks.index) + flash success
```

---

## 4. ステータス更新（POST `/tasks/{task_uuid}/status`）

```mermaid
sequenceDiagram
    actor U as User
    participant Req as UpdateTaskStatusRequest
    participant C as TaskController@updateStatus
    participant T as Task(Model)

    U->>Req: POST /tasks/{task_uuid}/status
    Req-->>C: validated(task_uuid, status)
    C->>T: updateStatusByUuid(task_uuid, status)
    T->>T: where(...)->update(...)
    T-->>C: bool(updated or exists)
    alt 更新成功
        C-->>U: redirect + flash success
    else 対象なし
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
    participant T as Task(Model)

    U->>Req: DELETE /tasks/{task_uuid}
    Req-->>C: validated(task_uuid)
    C->>T: deleteByUuid(task_uuid)
    T->>T: where(...)->delete()
    T-->>C: bool
    alt 削除成功
        C-->>U: redirect + flash success
    else 対象なし
        C-->>U: redirect + flash error
    end
```
