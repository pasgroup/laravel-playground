<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
     * バリデーションルール
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $title_max  = config('app.task_title_max');
        $detail_max = config('app.task_detail_max');

        return [
            'title' => ['required', 'string', 'max:' . $title_max],
            'detail' => ['nullable', 'string', 'max:' . $detail_max],
            'due_date' => ['nullable', 'date'],
        ];
    }

    /**
     * バリデーションエラーメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $title_max  = config('app.task_title_max');
        $detail_max = config('app.task_detail_max');

        return [
            'title.required' => 'タイトルを入力してください。',
            'title.max' => 'タイトルは' . $title_max . '文字以内で入力してください。',
            'detail.max' => '詳細は' . $detail_max . '文字以内で入力してください。',
            'due_date.date' => '期限日は有効な日付で入力してください。',
        ];
    }
}
