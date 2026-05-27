<style>
body {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: #f4f7fa;
}

.login-wrapper {
  width: 100%;
  max-width: 420px;
  padding: 20px;
}

.login-logo {
  text-align: center;
  margin-bottom: 28px;
}

.login-logo h1 {
  font-size: 32px;
  font-weight: bold;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.login-logo p {
  color: #999;
  font-size: 14px;
  margin-top: 4px;
}

.login-card {
  background: white;
  border-radius: 15px;
  box-shadow: #e2e8f0 0px 4px 6px;
  overflow: hidden;
}

.login-card-header {
  padding: 20px 25px;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
}

.login-card-header h2 {
  font-size: 20px;
  margin: 0;
}

.login-card-header p {
  font-size: 13px;
  margin-top: 4px;
  opacity: 0.85;
}

.login-card-body {
  padding: 28px 25px;
}

.error-list {
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 8px;
  padding: 12px 16px;
  margin-bottom: 20px;
  list-style: none;
}

.error-list li {
  color: #1e40af;
  font-size: 14px;
}

.error-list li + li {
  margin-top: 4px;
}

.btn-login {
  width: 100%;
  padding: 12px;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition:
    transform 0.2s,
    opacity 0.2s;
  margin-top: 4px;
}

.btn-login:hover {
  transform: translateY(-2px);
  opacity: 0.93;
}

.btn-login:active {
  transform: translateY(0);
}

/* Form group styles - adding these to match the login form */
.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin-bottom: 6px;
}

.form-group input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 14px;
  transition:
    border-color 0.2s,
    box-shadow 0.2s;
  box-sizing: border-box;
}

.form-group input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>