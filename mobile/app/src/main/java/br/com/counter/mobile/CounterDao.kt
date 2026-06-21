package br.com.counter.mobile

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query

@Dao
interface CounterDao {
    @Query("SELECT * FROM inventory_counts ORDER BY id DESC")
    suspend fun getCounts(): List<InventoryCountEntity>

    @Query("SELECT * FROM count_items WHERE countId = :countId ORDER BY productName")
    suspend fun getItems(countId: Int): List<CountItemEntity>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun saveCounts(counts: List<InventoryCountEntity>)

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun saveItems(items: List<CountItemEntity>)

    @Query("UPDATE count_items SET countedQuantity = :quantity, dirty = 1 WHERE id = :id")
    suspend fun updateQuantity(id: Int, quantity: Double?)

    @Query("SELECT * FROM count_items WHERE countId = :countId AND dirty = 1 ORDER BY productName")
    suspend fun dirtyItems(countId: Int): List<CountItemEntity>

    @Query("UPDATE count_items SET dirty = 0, syncStatus = 'synced' WHERE countId = :countId")
    suspend fun markSynced(countId: Int)
}
