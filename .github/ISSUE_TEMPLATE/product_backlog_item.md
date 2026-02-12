---
name: product_backlog_item
about: プロダクトバックログアイテム（PBI）のテンプレート
title: ''
labels: ''
assignees: ''

---

# PBI: <タイトル>

Labels: epic: xxx

---

## 0. Epic確認

- このPBIには原則1つのEpicのみ付与する
- 複数のEpicにまたがる場合はPBI分割を検討する
- 依存関係はEpicではなく「Notes」に記載する

---

## 1. User Story

### Who（誰のための機能か）
- 対象ユーザーを書く

### What（何をできるようにするか）
- システムの振る舞いを書く
- UIではなく「できること」を書く

### Why（なぜ必要か / どんな価値があるか）
- ユーザー価値
- ビジネス価値
- 技術的背景があれば記載

---

## 2. INVESTチェック

- [ ] Independent（他PBIに強く依存していない）
- [ ] Negotiable（実装詳細は議論可能）
- [ ] Valuable（ユーザー価値が明確）
- [ ] Estimable（見積もり可能）
- [ ] Small（2週間スプリントの10〜30%程度で完了できる）
- [ ] Testable（受け入れ基準で検証可能）

※ INVESTを満たさない場合は分割を検討する

---

## 3. Acceptance Criteria（受け入れ基準）

- [ ] 振る舞いベースで記載する
- [ ] テスト可能な条件である
- [ ] 必要に応じて Given / When / Then 形式で記載する

---

## 4. Tasks（Sub Issues）

- Sub-issueとして追加する
- 各Taskは本PBIのAcceptance Criteriaに対応させる

---

## 5. Definition of Done

- [ ] すべてのTaskが完了している
- [ ] テストが追加されている
- [ ] ローカルで動作確認済み
- [ ] 必要に応じてドキュメント更新済み

---

## 6. Notes（依存関係・補足）

- 他PBIとの依存があれば記載する
- 技術的制約があれば記載する
