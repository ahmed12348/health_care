@extends('admin.layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Create Product</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
            ← Back to Products
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                                <option value="">-- Select Category --</option>
                                @foreach(\App\Models\Category::all() as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Enter product name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="5"
                                      placeholder="Enter product description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Product Variants (Optional)</h5>
                        <div id="variants-container">
                            <div class="variant-item mb-3 p-3 border rounded">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Variant Type</label>
                                        <input type="text" 
                                               name="variants[0][variant_type]" 
                                               class="form-control" 
                                               placeholder="e.g., Size, Color">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Variant Value</label>
                                        <input type="text" 
                                               name="variants[0][variant_value]" 
                                               class="form-control" 
                                               placeholder="e.g., Large, Red">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Price</label>
                                        <input type="number" 
                                               step="0.01" 
                                               name="variants[0][variant_price]" 
                                               class="form-control" 
                                               placeholder="0.00">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-sm remove-variant" style="display:none;">
                                            ×
                                        </button>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Stock Quantity</label>
                                        <input type="number" 
                                               name="variants[0][variant_stock_quantity]" 
                                               class="form-control" 
                                               min="0" 
                                               value="0" 
                                               placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-variant">
                            + Add Variant
                        </button>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Product Image</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Image</label>
                                    <input type="file"
                                           name="image"
                                           class="form-control @error('image') is-invalid @enderror"
                                           accept="image/*"
                                           onchange="previewImage(this)">
                                    @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
                                </div>
                                
                                <div class="mb-3" id="image-preview" style="display: none;">
                                    <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 200px;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Pricing & Stock</h5>

                                <div class="mb-3">
                                    <label class="form-label">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number"
                                               step="0.01"
                                               name="price"
                                               class="form-control @error('price') is-invalid @enderror"
                                               value="{{ old('price') }}"
                                               placeholder="0.00">
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number"
                                           name="stock_quantity"
                                           class="form-control @error('stock_quantity') is-invalid @enderror"
                                           value="{{ old('stock_quantity', 0) }}"
                                           min="0"
                                           placeholder="0">
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="is_featured" 
                                               value="1" 
                                               id="is_featured"
                                               {{ old('is_featured') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            Featured Product
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="mt-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        Save Product
                    </button>
                </div>

            </form>
        </div>
    </div>

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }
    
    let variantCount = 1;
    
    document.getElementById('add-variant').addEventListener('click', function() {
        const container = document.getElementById('variants-container');
        const newVariant = container.firstElementChild.cloneNode(true);
        
        // Update input names with new index
        newVariant.querySelectorAll('input').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[0\]/, `[${variantCount}]`));
                input.value = '';
            }
        });
        
        // Show remove button
        newVariant.querySelector('.remove-variant').style.display = 'block';
        
        container.appendChild(newVariant);
        variantCount++;
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variant')) {
            if (document.getElementById('variants-container').children.length > 1) {
                e.target.closest('.variant-item').remove();
            }
        }
    });
</script>
@endpush

@endsection
