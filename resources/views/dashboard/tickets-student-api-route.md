Plan: create student tickets endpoint.

We detected there is no existing route/api for:
  GET /dashboard/my-tickets/api

We will add:
  Route::middleware('auth')->get('/dashboard/my-tickets/api', ...)

And return:
  { tickets: [{id, subject, status, created_at}], stats: {...} }

