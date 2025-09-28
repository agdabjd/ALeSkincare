// assets/js/main.js

// Helpers
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
            <i class="bi bi-cart" style="font-size:48px; color:#ffcfe6;"></i>
          </div>
        </div>
        <h5 class="mt-3">${escapeHtml(p.name)}</h5>
        <div class="text-success fw-bold">R$ ${Number(p.price).toFixed(2)}</div>
        <p class="small text-muted">${escapeHtml(p.description || '')}</p>
        <div class="mt-auto d-flex justify-content-between align-items-center">
          <div>
            <small>Fornecedor: <strong>${escapeHtml(p.supplier_name || '—')}</strong></small>
          </div>
          <div>
            ${inStock ? `<button class="btn btn-pink btn-sm btn-add" data-id="${p.id}">Adicionar</button>` : `<button class="btn btn-secondary btn-sm" disabled>Indisponível</button>`}
          </div>
        </div>
      </div>
    `;
    grid.appendChild(col);
  });

  // adiciona listeners
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

  // checkbox listeners
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

    // submit novo produto via AJAX
    document.getElementById('frmNewProduct').addEventListener('submit', async (ev) => {
      ev.preventDefault();
      const fd = new FormData(ev.target);
      const obj = Object.fromEntries(fd.entries());
      obj.in_stock = (fd.get('in_stock') ? 1 : 0);
      const res = await fetchJSON('php/api_products.php?action=create', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(obj)
      });
      if (res.ok) {
        alert('Produto cadastrado');
        ev.target.reset();
        document.getElementById('productForm').style.display = 'none';
        loadProducts();
      } else {
        alert('Erro ao cadastrar produto');
      }
    });

    // busca
    document.getElementById('searchBox').addEventListener('input', (e) => {
      loadProducts(e.target.value);
    });

    // adicionar selecionados
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

    // carregamento inicial
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

    // carrega lista
    async function loadSuppliers() {
      const sup = await fetchJSON('php/api_suppliers.php?action=list');
      const container = document.getElementById('suppliersTable');
      if (!container) return;
      let html = '<table class="table"><thead><tr><th>Nome</th><th>Contato</th><th>Endereço</th></tr></thead><tbody>';
      sup.forEach(s => {
        html += `<tr><td>${escapeHtml(s.name)}</td><td>${escapeHtml(s.contact || '')}</td><td>${escapeHtml(s.address || '')}</td></tr>`;
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
          itemsCont.innerHTML = '<div class="text-center p-4">Seu carrinho está vazio</div>';
        } else {
          let html = '<table class="table"><thead><tr><th>Produto</th><th>Preço</th><th>Ações</th></tr></thead><tbody>';
          data.items.forEach(it => {
            html += `<tr>
              <td><strong>${escapeHtml(it.name)}</strong><br><small>${escapeHtml(it.supplier_name||'')}</small></td>
              <td>R$ ${Number(it.price).toFixed(2)}</td>
              <td><button class="btn btn-outline-danger btn-sm" data-remove="${it.product_id}">Remover</button></td>
            </tr>`;
          });
          html += '</tbody></table>';
          itemsCont.innerHTML = html;

          // listeners remover
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
      if (summaryCont) {
        summaryCont.innerHTML = `<h5>Resumo do Pedido</h5>
          <p>Total de itens: ${data.count}</p>
          <p><strong>Total: R$ ${Number(data.total).toFixed(2)}</strong></p>
          <button class="btn btn-pink w-100">Finalizar Compra</button>`;
      }
    }
    loadCart();
  }
});
