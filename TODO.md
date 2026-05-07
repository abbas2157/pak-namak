- [ ] Add `bill_image` column to `sales` table via migration
- [ ] Add `bill_image` to `Sale` model fillable
- [ ] Update `admin/sales/create.blade.php`: add file input + multipart enctype
- [ ] Update `SaleController@store`: validate and upload bill image to `public/uploads/sales/bills/`, save path to DB
- [ ] Update receipt view to display bill image if present
- [x] Run migrations and smoke test sale creation + receipt




