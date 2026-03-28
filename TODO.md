# Stock Validation for Commands - Implementation Plan

## Current Status: Planning approved ✅

### Steps to Complete:

#### 1. ✅ Add `validateStock(array $details): array` private method to `classes/CommandeManager.php`
   - For each detail: SELECT quantite FROM poduits WHERE id = :id
   - If detail.quantite > product.quantite → collect error "Produit {nom} (ID {id}): stock disponible {stock}, demandé {quantite}"
   - Return ['success' => true] or ['success' => false, 'errors' => array]

#### 2. ✅ Update `createCommande()` in CommandeManager
   - Before `$this->pdo->beginTransaction()`
   - `$stockCheck = $this->validateStock($details);`
   - `if (!$stockCheck['success']) return ['success' => false, 'message' => 'Stock insuffisant pour certains produits: ' . implode(', ', $stockCheck['errors'])];`

#### 3. ✅ Update `updateCommande()` in CommandeManager  
   - After status check (`if (!$currentCmd || $currentCmd->getEtat() !== 'en_cours')`)
   - Before `$this->pdo->beginTransaction()`
   - Same validation as step 2

#### 4. ✅ Update `clotureCommande()` in CommandeManager
   - After status check
   - `$detailsData = $this->getCommandeDetails($id_commande)['details'];`
   - Format $details = [['quantite' => $d['quantite'], 'id_produit' => $d['id_produit']] ...]
   - `$stockCheck = $this->validateStock($details);`
   - If fail → return error as above
   - Only proceed with stock decrease if OK

#### 5. [ ] Testing
   - Create product with stock=5
   - Create command with quantite=10 → should BLOCK with message
   - Edit command to increase quantite>stock → BLOCK
   - Create OK command (quant<=stock), try cloture with another product quant>current stock → BLOCK
   - Verify stock never goes negative

### Notes:
- Table: poduits (note typo)
- Error messages shown via frontend alert(result.message)
- No frontend/backend changes needed beyond manager logic

**Next: Implement step 1 → check off → step 2 → etc.**

