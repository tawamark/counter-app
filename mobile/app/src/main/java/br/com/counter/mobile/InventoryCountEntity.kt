package br.com.counter.mobile

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "inventory_counts")
data class InventoryCountEntity(
    @PrimaryKey val id: Int,
    val title: String,
    val status: String,
    val itemsCount: Int
)
