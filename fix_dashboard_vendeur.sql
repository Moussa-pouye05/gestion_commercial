-- Fix for dashboard vendeur error: Add missing date_commande column to commandes table
-- and backfill for existing rows

ALTER TABLE commandes 
ADD COLUMN date_commande DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Backfill date for existing rows (if any)
UPDATE commandes SET date_commande = NOW() WHERE date_commande IS NULL;

-- Verify structure
-- DESCRIBE commandes;
