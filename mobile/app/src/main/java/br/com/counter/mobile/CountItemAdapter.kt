package br.com.counter.mobile

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import br.com.counter.mobile.databinding.RowCountItemBinding

class CountItemAdapter(
    private val onSave: (CountItemEntity, String) -> Unit
) : RecyclerView.Adapter<CountItemAdapter.ViewHolder>() {
    private val allItems = mutableListOf<CountItemEntity>()
    private val items = mutableListOf<CountItemEntity>()
    private var savingItemId: Int? = null
    private var searchTerm = ""

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = RowCountItemBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return items.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(items[position])
    }

    fun update(newItems: List<CountItemEntity>) {
        allItems.clear()
        allItems.addAll(newItems)
        applyFilter()
    }

    fun filter(term: String) {
        searchTerm = term.trim()
        applyFilter()
    }

    fun visibleCount(): Int {
        return items.size
    }

    fun updateSavingItem(itemId: Int?) {
        savingItemId = itemId
        notifyDataSetChanged()
    }

    private fun applyFilter() {
        val normalizedTerm = searchTerm.lowercase()
        val filteredItems = if (normalizedTerm.isBlank()) {
            allItems
        } else {
            allItems.filter {
                it.productName.lowercase().contains(normalizedTerm) ||
                    it.sku.orEmpty().lowercase().contains(normalizedTerm) ||
                    it.barcode.orEmpty().lowercase().contains(normalizedTerm)
            }
        }

        items.clear()
        items.addAll(filteredItems)
        notifyDataSetChanged()
    }

    inner class ViewHolder(private val binding: RowCountItemBinding) : RecyclerView.ViewHolder(binding.root) {
        fun bind(item: CountItemEntity) {
            val isSaving = savingItemId == item.id

            binding.textName.text = item.productName
            binding.textDetails.text = details(item)
            binding.textStatus.text = syncStatusLabel(item)
            binding.viewStatusDot.background = ContextCompat.getDrawable(binding.root.context, syncStatusDot(item))
            binding.editQuantity.setText(item.countedQuantity?.toString() ?: "")
            binding.editQuantity.isEnabled = !isSaving
            binding.buttonSave.isEnabled = !isSaving
            binding.buttonSave.text = if (isSaving) "Salvando" else "Salvar"
            binding.buttonSave.setOnClickListener {
                onSave(item, binding.editQuantity.text.toString())
            }
        }

        private fun details(item: CountItemEntity): String {
            val sku = item.sku ?: "SKU não informado"
            val unit = item.unit ?: "un"
            val barcode = item.barcode?.takeIf { it.isNotBlank() }?.let { " • Código: $it" } ?: ""
            return "$sku$barcode • Sistema: ${item.systemQuantity} $unit"
        }

        private fun syncStatusLabel(item: CountItemEntity): String {
            return if (item.dirty) {
                "Pendente de sincronização"
            } else {
                when (item.syncStatus) {
                    "synced" -> "Sincronizado"
                    "pending" -> "Pendente"
                    else -> item.syncStatus
                }
            }
        }

        private fun syncStatusDot(item: CountItemEntity): Int {
            return if (item.dirty || item.syncStatus == "pending") {
                R.drawable.bg_status_progress
            } else {
                R.drawable.bg_status_approved
            }
        }
    }
}
