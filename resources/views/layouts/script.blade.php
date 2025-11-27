  <!-- scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
      // --- Demo dataset (replace with API/Laravel data) ---
      const demo = {
        sales: { labels: [...Array(30)].map((_,i)=>`يوم ${i+1}`), data: Array.from({length:30},()=>Math.floor(10000+Math.random()*30000)) },
        visitors: { labels:['الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت','الأحد'], data:[120,190,150,220,170,240,300] },
        categories: { labels:['إلكترونيات','موضة','منزل','رياضة'], data:[320,210,180,140] },
        activities: [
          {text:'طلب جديد من محمد أحمد', time:'5 دقائق'},
          {text:'تم إضافة منتج جديد', time:'12 دقيقة'},
          {text:'تم تحديث الطلب #1254', time:'25 دقيقة'},
          {text:'مستخدم جديد سجل', time:'40 دقيقة'}
        ],
        orders: Array.from({length:47}).map((_,i)=>({id:1250+i, customer:['أحمد','ليلى','سامي','مروة'][i%4], amount:(50+Math.floor(Math.random()*500)) + ' ر.س', status:['قيد الانتظار','مكتمل','ملغى'][i%3]}))
      }

      // --- Theme toggle (FIXED) ---
      const themeToggle = document.getElementById('themeToggle');
      
      function setTheme(dark){
        if(dark){
          document.body.classList.add('dark-mode');
          localStorage.setItem('demoDark','1');
          themeToggle.innerHTML='<i class="fas fa-sun"></i>';
        } else {
          document.body.classList.remove('dark-mode');
          localStorage.removeItem('demoDark');
          themeToggle.innerHTML='<i class="fas fa-moon"></i>';
        }
      }
      
      themeToggle.addEventListener('click',()=>{
        const isDark = document.body.classList.contains('dark-mode');
        setTheme(!isDark);
      });
      
      // Load saved theme on page load
      if(localStorage.getItem('demoDark')) {
        setTheme(true);
      }

      // --- Flatpickr range ---
      flatpickr('#dateRange',{mode:'range',dateFormat:'Y-m-d',locale:'ar'});

      // --- Charts ---
      const salesCtx = document.getElementById('salesChart');
      const salesChart = new Chart(salesCtx,{
        type:'line',
        data:{labels:demo.sales.labels,datasets:[{label:'مبيعات',data:demo.sales.data,tension:.35,fill:true,backgroundColor:'rgba(99,102,241,.08)',borderColor:'#6366f1',pointRadius:0}]},
        options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
      });

      const visitorsCtx = document.getElementById('visitorsChart');
      new Chart(visitorsCtx,{type:'bar',data:{labels:demo.visitors.labels,datasets:[{label:'زوار',data:demo.visitors.data,backgroundColor:'rgba(13,110,253,.9)'}]},options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});

      const catCtx = document.getElementById('categoryChart');
      new Chart(catCtx,{type:'doughnut',data:{labels:demo.categories.labels,datasets:[{data:demo.categories.data,backgroundColor:['#6d28d9','#ef4444','#06b6d4','#10b981']}]},options:{plugins:{legend:{position:'bottom'}}}});

      // --- Activity feed ---
      const activityFeed = document.getElementById('activityFeed');
      demo.activities.forEach(a=>{const el=document.createElement('div');el.className='list-group-item d-flex justify-content-between align-items-start py-2';el.innerHTML=`<div>${a.text}<div class="small-muted">${a.time}</div></div>`;activityFeed.appendChild(el)});

      // --- Orders table + pagination ---
      const ordersPerPage = 6;
      const ordersTableBody = document.querySelector('#ordersTable tbody');
      const paginationEl = document.getElementById('ordersPagination');
      let currentPage = 1;

      function renderOrders(){
        ordersTableBody.innerHTML='';
        const start=(currentPage-1)*ordersPerPage;const pageItems=demo.orders.slice(start,start+ordersPerPage);
        pageItems.forEach(o=>{
          const tr=document.createElement('tr');
          tr.innerHTML=`<td>#${o.id}</td><td>${o.customer}</td><td>${o.amount}</td><td><span class="badge bg-${o.status==='مكتمل'?'success':o.status==='قيد الانتظار'?'warning':'danger'}">${o.status}</span></td>`;
          tr.addEventListener('click',()=>openOrderModal(o));
          ordersTableBody.appendChild(tr);
        });
        renderPagination();
      }

      function renderPagination(){
        const pages=Math.ceil(demo.orders.length/ordersPerPage);paginationEl.innerHTML='';
        for(let p=1;p<=pages;p++){const li=document.createElement('li');li.className='page-item '+(p===currentPage?'active':'');li.innerHTML=`<a class="page-link" href="#">${p}</a>`;li.addEventListener('click',e=>{e.preventDefault();currentPage=p;renderOrders()});paginationEl.appendChild(li)}
      }

      function openOrderModal(order){
        const body=document.getElementById('orderModalBody');
        body.innerHTML=`<p><strong>رقم الطلب:</strong> #${order.id}</p><p><strong>العميل:</strong> ${order.customer}</p><p><strong>المبلغ:</strong> ${order.amount}</p><p><strong>الحالة:</strong> ${order.status}</p>`;
        const modal = new bootstrap.Modal(document.getElementById('orderModal'));
        modal.show();
      }

      renderOrders();

      // --- CSV export (orders) ---
      document.getElementById('exportCsvBtn').addEventListener('click',()=>{
        const rows=[['رقم','العميل','المبلغ','الحالة'],...demo.orders.map(o=>[o.id,o.customer,o.amount,o.status])];
        const csv = rows.map(r=>r.map(cell=>`"${String(cell).replace(/"/g,'""')}"`).join(',')).join('\n');
        const blob=new Blob([csv],{type:'text/csv;charset=utf-8;'});
        const url=URL.createObjectURL(blob);const a=document.createElement('a');a.href=url;a.download='orders.csv';document.body.appendChild(a);a.click();a.remove();URL.revokeObjectURL(url);
      });

      // --- simple search (client-side demo) ---
      function doSearch(){
        const q=document.getElementById('globalSearch').value.trim();
        if(!q) return alert('اكتب كلمة للبحث');
        // demo: search in orders customer
        const found = demo.orders.filter(o=>String(o.customer).includes(q));
        if(found.length===0) return alert('لا نتائج');
        // show first found order
        openOrderModal(found[0]);
      }

      // --- optional: hook to Laravel (examples) ---
      // Replace demo data with fetch('/api/dashboard') then update charts and tables
      // Example: fetch('/api/dashboard').then(r=>r.json()).then(data=>{ /* update demo and call chart.update() */ })

      // Accessibility: close offcanvas on link click (mobile)
      document.querySelectorAll('#sidebarOffcanvas .nav-link').forEach(a=>a.addEventListener('click',()=>{const off=document.getElementById('sidebarOffcanvas');const bs=bootstrap.Offcanvas.getInstance(off);if(bs) bs.hide();}));
    </script>
    @yield('script')