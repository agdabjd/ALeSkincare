async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  return res.json();
}

/* ---------- Produtos ---------- */
async function loadProducts(filter = '') {
  const data = await fetchJSON('php/api_products.php?action=list');
  const grid = document.getElementById('productsGrid');
  if (!grid) return;
  grid.innerHTML = '';
  const filtered = data.filter(p => p.name.toLowerCase().includes(filter.toLowerCase()));
  filtered.forEach(p => {
    const col = document.createElement('div');
    col.className = 'col-md-4';
    const inStock = p.in_stock == 1;
    col.innerHTML = `
      <div class="card p-3 h-100">
        <div class="position-relative">
          <input type="checkbox" class="select-product" data-id="${p.id}" style="position:absolute; right:12px; top:12px;">
          <div style="height:140px; background:#ffeef6; border-radius:8px; display:flex; align-items:center; justify-content:center;">
            <img src="assets/cart-product.png">
          </div>
        </div>
        <h5 class="mt-3" style="color: #d60464;">${escapeHtml(p.name)}</h5>
        <h4 class="text-success fw-bold">R$ ${Number(p.price).toFixed(2)}</h4>
        <p class="small text-muted">${escapeHtml(p.description || '')}</p>
        <div class="mt-auto">
          <div class="text-pink mb-2">
            <small><strong>Fornecedor: </strong>${escapeHtml(p.supplier_name || '—')}</small>
          </div>
          <div class="d-flex gap-2">
            ${inStock 
              ? `<button class="btn btn-pink flex-grow-1 btn-add" data-id="${p.id}">
                   <img src="assets/cart-white-small.png"> Adicionar
                 </button>` 
              : `<button class="btn flex-grow-1 btn-outline-light" style=" background-color:#C4C4C7;" disabled>
                    <img src="assets/cart-white-small.png"> Indisponível
                 </button>`}
            <button class="btn btn-outline-pink btn-edit" data-id="${p.id}">
              <img src="assets/edit.png">
            </button>
          </div>
        </div>
      </div>
    `;
    grid.appendChild(col);
  });

  document.querySelectorAll('.btn-add').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const id = e.currentTarget.getAttribute('data-id');
      const res = await fetchJSON('php/api_basket.php?action=add', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({product_id: id})
      });
      if (res.ok) {
        alert(`Produto adicionado! itens na cesta: ${res.count}`);
      } else {
        alert('Erro ao adicionar ao carrinho.');
      }
      updateSelectedButton();
    });
  });

document.querySelectorAll('.btn-edit').forEach(btn => {
  btn.addEventListener('click', async (e) => {
    const id = e.currentTarget.getAttribute('data-id');
    const product = await fetchJSON(`php/api_products.php?action=get&id=${id}`);

    if (product) {
      document.getElementById('productForm').style.display = '';
      document.querySelector("#frmNewProduct button[type='submit']").textContent = "Salvar Alterações";
      document.getElementById('productId').value = product.id ?? '';
      document.getElementById('productName').value = product.name ?? '';
      document.getElementById('productPrice').value = product.price ?? '';
      document.getElementById('productDescription').value = product.description ?? '';

      const sel = document.getElementById('selectSupplier');
      if (product.supplier_id) {
        let opt = [...sel.options].find(o => o.value == product.supplier_id);
        if (!opt) {
          opt = document.createElement('option');
          opt.value = product.supplier_id;
          opt.textContent = product.supplier_name || `Fornecedor #${product.supplier_id}`;
          sel.appendChild(opt);
        }
        sel.value = product.supplier_id;
      } else {
        sel.value = '';
      }

      document.getElementById('inStock').checked = (parseInt(product.in_stock) === 1);
      document.getElementById('frmNewProduct').setAttribute('data-action', 'update');

      document.getElementById("btnCancelProduct").addEventListener("click", function() {
        document.getElementById("frmNewProduct").reset();
        document.getElementById("productId").value = "";
        document.getElementById("productTitle").textContent = "Cadastrar Novo Produto";
        document.querySelector("#frmNewProduct button[type='submit']").textContent = "Cadastrar Produto";
        document.getElementById("productForm").style.display = "none";
      });

    }
  });
});

  document.querySelectorAll('.select-product').forEach(cb => {
    cb.addEventListener('change', updateSelectedButton);
  });
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; });
}

function updateSelectedButton() {
  const btn = document.getElementById('btnAddSelected');
  if (!btn) return;
  const checked = document.querySelectorAll('.select-product:checked').length;
  btn.disabled = checked === 0;
  btn.textContent = checked ? `Adicionar selecionados (${checked})` : 'Adicionar selecionados';
}

/* ---------- Formulário novo produto ---------- */
document.addEventListener('DOMContentLoaded', async () => {
  // se estamos na página produtos
  if (document.getElementById('btnNewProduct')) {
    document.getElementById('btnNewProduct').addEventListener('click', () => {
      document.getElementById('productForm').style.display = '';
    });
    document.getElementById('btnCancelProduct').addEventListener('click', () => {
      document.getElementById('productForm').style.display = 'none';
    });

    // carrega fornecedores no select
    const suppliers = await fetchJSON('php/api_suppliers.php?action=list');
    const sel = document.getElementById('selectSupplier');
    suppliers.forEach(s => {
      const opt = document.createElement('option');
      opt.value = s.id;
      opt.textContent = s.name;
      sel.appendChild(opt);
    });

    document.getElementById('frmNewProduct').addEventListener('submit', async (ev) => {
      ev.preventDefault();
      const fd = new FormData(ev.target);
      const obj = Object.fromEntries(fd.entries());
      obj.in_stock = (fd.get('in_stock') ? 1 : 0);

      const action = ev.target.getAttribute('data-action') === 'update' ? 'update' : 'create';
      const res = await fetchJSON(`php/api_products.php?action=${action}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(obj)
      });

      if (res.ok) {
        alert(action === 'create' ? 'Produto cadastrado' : 'Produto atualizado');
        ev.target.reset();
        document.getElementById('productForm').style.display = 'none';
        ev.target.removeAttribute('data-action');
        loadProducts();
      } else {
        alert('Erro ao salvar produto');
      }
    });

    document.getElementById('searchBox').addEventListener('input', (e) => {
      loadProducts(e.target.value);
    });

    document.getElementById('btnAddSelected').addEventListener('click', async () => {
      const checked = [...document.querySelectorAll('.select-product:checked')].map(el => el.getAttribute('data-id'));
      if (!checked.length) return alert('Selecione ao menos um produto.');
      const res = await fetchJSON('php/api_basket.php?action=add', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({product_ids: checked})
      });
      if (res.ok) {
        alert(`Foram adicionados ${res.added} itens. Total na cesta: ${res.count}`);
        // limpa selects
        document.querySelectorAll('.select-product:checked').forEach(c => c.checked = false);
        updateSelectedButton();
      } else {
        alert('Erro ao adicionar selecionados');
      }
    });

    loadProducts();
  }

  /* ---------- Fornecedores ---------- */
  if (document.getElementById('btnNewSupplier')) {
    document.getElementById('btnNewSupplier').addEventListener('click', () => {
      document.getElementById('supplierForm').style.display = '';
    });
    document.getElementById('btnCancelSupplier').addEventListener('click', () => {
      document.getElementById('supplierForm').style.display = 'none';
    });

    async function loadSuppliers() {
      const sup = await fetchJSON('php/api_suppliers.php?action=list');
      const container = document.getElementById('suppliersTable');
      if (!container) return;
      let html = '<table class="table"><thead><tr><th><p style="color: #d60464; font-weight: bold;">Nome</p></th><th><p style="color: #d60464; font-weight: bold;">Contato</p></th><th><p style="color: #d60464; font-weight: bold;">Endereço</p></th></tr></thead><tbody>';
      sup.forEach(s => {
        html += `<tr><td style="color: #d60464; font-weight: bold;">${escapeHtml(s.name)}</td><td class="text-muted"><img src="assets/contact-pink.png">${escapeHtml(s.contact || '')}</td><td class="text-muted"><img src="assets/location-pink.png">${escapeHtml(s.address || '')}</td></tr>`;
      });
      html += '</tbody></table>';
      container.innerHTML = html;
    }

    document.getElementById('frmNewSupplier').addEventListener('submit', async (ev) => {
      ev.preventDefault();
      const fd = new FormData(ev.target);
      const obj = Object.fromEntries(fd.entries());
      const res = await fetchJSON('php/api_suppliers.php?action=create', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify(obj)
      });
      if (res.ok) {
        alert('Fornecedor cadastrado');
        ev.target.reset();
        document.getElementById('supplierForm').style.display = 'none';
        loadSuppliers();
      } else {
        alert('Erro ao cadastrar fornecedor');
      }
    });

    loadSuppliers();
  }

  /* ---------- Carrinho ---------- */
  if (document.getElementById('cartItems') || document.getElementById('cartSummary')) {
    async function loadCart() {
      const data = await fetchJSON('php/api_basket.php?action=get');
      const itemsCont = document.getElementById('cartItems');
      const summaryCont = document.getElementById('cartSummary');
      if (itemsCont) {
        if (!data.items.length) {
          itemsCont.className = "col-12";
          itemsCont.innerHTML = `
            <div class="card p-5 text-center shadow-sm w-100" style="background-color:#ffffff; border-radius: 1rem;">
              <img src="assets/cart-empty.png" alt="Carrinho vazio" style="width:80px; margin:auto;">
              <h4 class="cart-empty-title mt-3 text-pink">Seu carrinho está vazio</h4>
              <p class="cart-empty-subtitle text-pink">
                Adicione produtos da tela de produtos para começar suas compras.
              </p>
              <div class="cart-extra-info text-muted mt-2">
                ✨ Frete grátis acima de R$ 99 &nbsp; • &nbsp; 🔒 Compra 100% segura
              </div>
            </div>
          `;

          const summaryCont = document.getElementById('cartSummary');
          if (summaryCont) summaryCont.innerHTML = "";
        } else {
          itemsCont.className = "col-md-8";
          let html = '<table class="table shadow-sm" style="outline: 1px solid #FDBBD5; border-radius: 5px;"><thead><tr><th><p style="color: #d60464; font-weight: bold;">Produto</p></th><th><p style="color: #d60464; font-weight: bold;">Preço</p></th><th><p style="color: #d60464; font-weight: bold;">Ações</p></th></tr></thead><tbody>';
          data.items.forEach(it => {
            html += `<tr>
              <td><strong style="color: #d60464;">${escapeHtml(it.name)}</strong><br><small>${escapeHtml(it.supplier_name||'')}</small></td>
              <td><strong>R$ ${Number(it.price).toFixed(2)}</strong></td>
              <td><button class="btn btn-outline-pink btn-sm" data-remove="${it.product_id}"><img src="assets/delete-red.png"></button></td>
            </tr>`;
          });
          html += '</tbody></table>';
          itemsCont.innerHTML = html;

          itemsCont.querySelectorAll('[data-remove]').forEach(btn => {
            btn.addEventListener('click', async (e) => {
              const pid = e.currentTarget.getAttribute('data-remove');
              const res = await fetchJSON('php/api_basket.php?action=remove', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({product_id: pid})
              });
              if (res.ok) loadCart();
              else alert('Erro ao remover item');
            });
          });
        }
      }
      if (data.items.length) {
        summaryCont.innerHTML = `
          <div style="background-color: #ffffff; border: 1px solid #f5c0d4; border-radius: 5px; padding: 15px;" class="shadow-sm">
            <h5 style="color: #c2185b;">Resumo do Pedido</h5>
            <p style="color: #6c757d; margin-bottom: 5px;">Total de itens: <strong style="color: #000;">${data.count}</strong></p> 
            <p style="color: #c2185b; font-weight: bold; font-size: 1.1rem; margin-bottom: 10px;">Total: R$ ${Number(data.total).toFixed(2)}</p>
            <button class="btn btn-pink w-100" style="margin-bottom: 10px;" id="finalizePurchaseBtn">
              <img src="assets/card-white.png">
              Finalizar Compra
            </button>
            <small style="color: #6c757d; display: block; text-align: center;">
              Pagamento seguro e entrega garantida<br>
              ✓ SSL Certificado &nbsp; ✓ Compra Protegida
            </small>
          </div>
          `;

          const finalizeBtn = document.getElementById('finalizePurchaseBtn');
          if (finalizeBtn) {
            finalizeBtn.addEventListener('click', async () => {
              // 1. Chamar a API para limpar o carrinho (nova action: clear)
              const res = await fetchJSON('php/api_basket.php?action=clear', {
                method: 'POST',
                headers: {'Content-Type':'application/json'}
              });
              
              if (res.ok) {
                alert('🎉 Compra finalizada com sucesso! Seu carrinho foi esvaziado.');
                loadCart(); 
              } else {
                alert('⚠️ Erro ao finalizar a compra. Tente novamente.');
              }
            });
          }          
      }
    }
    loadCart();
  }
});