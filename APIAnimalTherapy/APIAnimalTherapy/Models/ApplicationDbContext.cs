using Microsoft.EntityFrameworkCore;

namespace APIAnimalTherapy.Models
{
    public class ApplicationDbContext : DbContext
    {
        public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options) : base(options) { }

        public DbSet<User> Users { get; set; }
        public DbSet<Admin> Admins { get; set; }
        public DbSet<Animal> Animals { get; set; }
        public DbSet<Bitacora> Bitacoras { get; set; }
        public DbSet<Cliente> Clientes { get; set; }
        public DbSet<Especialista> Especialistas { get; set; }
        public DbSet<Seguimiento> Seguimientos { get; set; }
        public DbSet<Terapia> Terapias { get; set; }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            // Relación User -> Admin (Uno a uno)
            modelBuilder.Entity<Admin>()
                .HasOne(a => a.User)
                .WithMany()
                .HasForeignKey(a => a.User_usuario_id)
                .OnDelete(DeleteBehavior.NoAction);

            // Relación User -> Cliente (Uno a uno)
            modelBuilder.Entity<Cliente>()
                .HasOne(c => c.user)
                .WithMany()
                .HasForeignKey(c => c.User_usuario_id)
                .OnDelete(DeleteBehavior.Cascade);

            // Relación User -> Especialista (Uno a uno)
            modelBuilder.Entity<Especialista>()
                .HasOne(e => e.user)
                .WithMany()
                .HasForeignKey(e => e.User_usuario_id)
                .OnDelete(DeleteBehavior.Cascade);

            // Relación Bitacora -> Admin (Uno a muchos)
            modelBuilder.Entity<Bitacora>()
                .HasOne(b => b.Admin)
                .WithMany()
                .HasForeignKey(b => b.Admin_admin_id)
                .OnDelete(DeleteBehavior.Cascade);

            // Relación Bitacora -> Especialista (Uno a muchos)
            modelBuilder.Entity<Bitacora>()
                .HasOne(b => b.Especialista)
                .WithMany()
                .HasForeignKey(b => b.Especialista_especialista_id)
                .OnDelete(DeleteBehavior.Cascade);

            // Relación Terapia -> Cliente (Uno a muchos)
            modelBuilder.Entity<Terapia>()
                .HasOne(t => t.cliente)
                .WithMany(c => c.terapias)
                .HasForeignKey(t => t.Cliente_cliente_id)
                .OnDelete(DeleteBehavior.Cascade);

            // Relación Terapia -> Especialista (Uno a muchos)
            modelBuilder.Entity<Terapia>()
                .HasOne(t => t.especialista)
                .WithMany(e => e.terapias)
                .HasForeignKey(t => t.Especialista_especialista_id)
                .OnDelete(DeleteBehavior.Cascade);

            // Relación Terapia -> Animal (Uno a muchos), pero la relación con Animal es opcional
            modelBuilder.Entity<Terapia>()
                .HasOne(t => t.animal)
                .WithMany(a => a.terapias)
                .HasForeignKey(t => t.Animal_animal_id)
                .OnDelete(DeleteBehavior.SetNull); // Cambiado a SetNull para que sea opcional

            // Relación Seguimiento -> Terapia (Uno a muchos)
            modelBuilder.Entity<Seguimiento>()
                .HasOne(s => s.terapia)
                .WithMany(t => t.seguimientos)
                .HasForeignKey(s => s.Terapia_idTerapia)
                .OnDelete(DeleteBehavior.Cascade);

            base.OnModelCreating(modelBuilder);
        }
    }
}
