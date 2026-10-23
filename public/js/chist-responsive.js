document.addEventListener('DOMContentLoaded',()=>{
 const clean=s=>(s||'').replace(/\s+/g,' ').trim();
 document.querySelectorAll('.table-responsive table:not([data-no-mobile-cards])').forEach(table=>{
  if(table.closest('.ch-data-desktop')||table.dataset.cardsReady)return;
  const heads=[...table.querySelectorAll('thead th')].map(x=>clean(x.textContent)); const body=table.tBodies[0]; if(!body||!heads.length)return;
  const source=table.closest('.table-responsive'); source.classList.add('ch-auto-source'); table.dataset.cardsReady='1';
  const wrap=document.createElement('div'); wrap.className='ch-auto-cards';
  [...body.rows].forEach(row=>{ if(row.hidden||row.classList.contains('d-none'))return; const cells=[...row.cells]; if(cells.length<2)return;
   const card=document.createElement('article'); card.className='ch-auto-card'; const grid=document.createElement('div'); grid.className='ch-auto-card-grid';
   cells.forEach((cell,i)=>{ const label=heads[i]||''; const box=document.createElement('div'); const isActions=/действ|управ|операц/i.test(label); box.className='ch-auto-field'+(i===0?' primary':'')+(isActions?' actions':''); if(label){const l=document.createElement('small');l.textContent=label;box.appendChild(l)} const value=document.createElement('div');value.className='ch-auto-value';[...cell.childNodes].forEach(n=>value.appendChild(n.cloneNode(true)));box.appendChild(value);grid.appendChild(box)});
   card.appendChild(grid);wrap.appendChild(card);
  }); source.insertAdjacentElement('afterend',wrap);
 });
 document.querySelectorAll('form').forEach(f=>{if(f.querySelector('input[type="search"],input[name="q"],select[name="status"]')&&!f.classList.contains('ch-toolbar'))f.classList.add('ch-toolbar')});
});
