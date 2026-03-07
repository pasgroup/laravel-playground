<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class DestroyTaskRequest extends FormRequest
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
        $message = $validator->errors()->first('task_uuid');

        throw new HttpResponseException(
            Redirect::route('tasks.index')->with('error', $message)
        );
    }
}
