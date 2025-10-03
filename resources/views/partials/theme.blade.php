<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  :root {
    --pink-100: #ffd6e1;
    --pink-200: #ffb6ce;
    --accent: #ff69b4;
    --accent-dark: #d63384;
    --text: #333;
    --danger: #dc3545;
  }
  body.theme-gradient {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, var(--pink-100) 0%, var(--pink-200) 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: auto;
    padding: 24px;
  }
  body.theme-gradient::before {
    content: '';
    position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 50px 50px; animation: theme-float 20s ease-in-out infinite;
  }
  @keyframes theme-float { 0%,100% { transform: translateY(0) rotate(0); } 50% { transform: translateY(-20px) rotate(180deg); } }
  .card-ui { background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.2); border: 1px solid rgba(255, 182, 206, 0.3); position: relative; z-index: 1; }
  .card-header-ui { display: flex; gap: 16px; align-items: center; padding: 28px 32px 0 32px; }
  .logo-ui { width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #fff 0%, #ffeef2 100%); border: 3px solid rgba(255, 105, 180, 0.2); display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 10px 30px rgba(255,105,180,0.2); }
  .logo-ui img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }
  .title-ui { font-size: 26px; font-weight: 700; background: linear-gradient(135deg, var(--accent), var(--accent-dark)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
  .card-body-ui { padding: 24px 32px 32px 32px; }
  .grid-ui { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .grid-full { grid-column: 1 / -1; }
  .form-group-ui { display: flex; flex-direction: column; gap: 6px; }
  .label-ui { color: var(--text); font-weight: 600; font-size: 14px; }
  .input-ui, .select-ui, .textarea-ui { width: 100%; padding: 12px 14px; border: 2px solid rgba(255,105,180,0.2); border-radius: 12px; font-size: 14px; background: rgba(255,255,255,0.8); transition: all .2s ease; }
  .input-ui:focus, .select-ui:focus, .textarea-ui:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px rgba(255,105,180,0.15), 0 8px 25px rgba(255,105,180,0.2); background: rgba(255,255,255,0.95); transform: translateY(-1px); }
  .btn-row-ui { display: flex; gap: 12px; justify-content: flex-end; margin-top: 8px; }
  .btn-ui { padding: 12px 18px; border: none; border-radius: 12px; color: #fff; font-weight: 600; cursor: pointer; box-shadow: 0 8px 25px rgba(0,0,0,0.08); transition: all .2s; }
  .btn-ui:active { transform: translateY(0); }
  .btn-secondary-ui { background: linear-gradient(135deg, #6c757d, #495057); }
  .btn-primary-ui { background: linear-gradient(135deg, var(--accent), var(--accent-dark)); }
  .btn-ui:hover { transform: translateY(-1px); }
  .error-ui { color: var(--danger); font-size: 12px; }
  .alert-ui { background-color: #f8d7da; color: #721c24; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #f5c6cb; font-size: 14px; }
  .success-ui { background-color: #d4edda; color: #155724; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #c3e6cb; font-size: 14px; position: relative; }
  @media (max-width: 860px) { .grid-ui { grid-template-columns: 1fr; } .card-header-ui { padding: 20px 20px 0 20px; } .card-body-ui { padding: 20px; } }
</style>

