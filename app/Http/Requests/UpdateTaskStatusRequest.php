<?php

namespace App\Http\Requests;

use App\Models\Task;
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
                Rule::exists('tasks', 'task_uuid')->withoutTrashed(),
            ],
            'status' => [
                'required',
                'string',
                Rule::in([
                    Task::STATUS_NOT_STARTED,
                    Task::STATUS_IN_PROGRESS,
                    Task::STATUS_COMPLETED,
                ]),
            ],
        ];
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
            'task_uuid.exists' => '指定されたタスクは存在しないか、既に削除されています。',
            'status.required' => 'ステータスを指定してください。',
            'status.string' => 'ステータスの形式が不正です。',
            'status.in' => 'ステータスは未着手・進行中・完了のいずれかを指定してください。',
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
