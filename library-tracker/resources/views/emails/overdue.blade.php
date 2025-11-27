<p>Hello {{ $user->name }},</p>

<p>The following book is overdue:</p>

<ul>
  <li><strong>Title:</strong> {{ $book->title }}</li>
  <li><strong>Due Date:</strong> {{ $loan->due_at->toDateString() }}</li>
</ul>

<p>Please return it as soon as possible.</p>
