<?php

namespace App\Http\Requests;

use App\Domain\Task\TaskStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class UpdateTaskStatusRequest extends FormRequest
{
    /**
     * リクエストの認可
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * データを準備（ルートパラメータをマージ）
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'task_uuid' => $this->route('task_uuid'),
        ]);
    }

    /**
     * バリデーションルール
     *
     * @return array<string, array<int, string|\Illuminate\Validation\Rule>>
     */
    public function rules(): array
    {
        return [
            'task_uuid' => [
                'required',
                'uuid',
            ],
            'status' => [
                'required',
                'string',
                Rule::in($this->statusValues()),
            ],
        ];
    }

    /**
     * @return list<string>
     */
    private function statusValues(): array
    {
        return array_map(
            fn (TaskStatus $task_status): string => $task_status->value,
            TaskStatus::cases()
        );
    }

    /**
     * ステータスのラベル一覧（エラーメッセージ用）
     *
     * @return list<string>
     */
    private function statusLabels(): array
    {
        return array_map(
            fn (TaskStatus $task_status): string => $task_status->label(),
            TaskStatus::cases()
        );
    }

    /**
     * バリデーションエラーメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'task_uuid.required' => 'タスクを指定してください。',
            'task_uuid.uuid' => 'タスクの指定が不正です。',
            'status.required' => 'ステータスを指定してください。',
            'status.string' => 'ステータスの形式が不正です。',
            'status.in' => 'ステータスは' . implode('・', $this->statusLabels()) . 'のいずれかを指定してください。',
        ];
    }

    /**
     * バリデーション失敗時の処理（一覧へリダイレクト）
     *
     * @param Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        $key = $validator->errors()->has('task_uuid') ? 'task_uuid' : 'status';
        $message = $validator->errors()->first($key);

        throw new HttpResponseException(
            Redirect::route('tasks.index')->with('error', $message)
        );
    }
}
