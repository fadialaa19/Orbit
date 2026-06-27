# TODO_PAYMENTS_CHECKOUT

- [ ] Create student checkout page for Premium application (route GET/POST)
- [ ] Update scholarship details page: premium button should open checkout page (not POST to show)
- [ ] Implement controller logic to create Order in DB with status=pending
- [ ] Add form inputs required by Order migration: payment_method, transaction_id, bank_name, transfer_from, receipt_image
- [ ] Add student settings tab/section to show orders by status (pending/paid/failed)
- [ ] Ensure admin orders page already shows data; verify it works with new orders
- [ ] Run basic sanity test: submit checkout creates Order; student settings shows it; admin orders shows it

