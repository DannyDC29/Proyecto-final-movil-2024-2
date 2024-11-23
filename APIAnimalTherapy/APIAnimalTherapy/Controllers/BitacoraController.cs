using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using APIAnimalTherapy.Models;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace APIAnimalTherapy.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class BitacoraController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public BitacoraController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Bitacora
        [HttpGet]
        public async Task<ActionResult<IEnumerable<Bitacora>>> GetBitacoras()
        {
            return await _context.Bitacoras
                .Include(b => b.Admin) // Incluir la relación con Admin
                .Include(b => b.Especialista) // Incluir la relación con Especialista
                .ToListAsync();
        }

        // GET: api/Bitacora/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Bitacora>> GetBitacora(int id)
        {
            var bitacora = await _context.Bitacoras
                .Include(b => b.Admin) // Incluir la relación con Admin
                .Include(b => b.Especialista) // Incluir la relación con Especialista
                .FirstOrDefaultAsync(b => b.bitacora_id == id);

            if (bitacora == null)
            {
                return NotFound();
            }

            return bitacora;
        }

        // POST: api/Bitacora
        [HttpPost]
        public async Task<ActionResult<Bitacora>> CreateBitacora(Bitacora bitacora)
        {
            _context.Bitacoras.Add(bitacora);
            await _context.SaveChangesAsync();

            return CreatedAtAction(nameof(GetBitacora), new { id = bitacora.bitacora_id }, bitacora);
        }

        // PUT: api/Bitacora/5
        [HttpPut("{id}")]
        public async Task<IActionResult> UpdateBitacora(int id, Bitacora bitacora)
        {
            if (id != bitacora.bitacora_id)
            {
                return BadRequest();
            }

            _context.Entry(bitacora).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!BitacoraExists(id))
                {
                    return NotFound();
                }
                else
                {
                    throw;
                }
            }

            return NoContent();
        }

        // DELETE: api/Bitacora/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteBitacora(int id)
        {
            var bitacora = await _context.Bitacoras.FindAsync(id);
            if (bitacora == null)
            {
                return NotFound();
            }

            _context.Bitacoras.Remove(bitacora);
            await _context.SaveChangesAsync();

            return NoContent();
        }

        private bool BitacoraExists(int id)
        {
            return _context.Bitacoras.Any(e => e.bitacora_id == id);
        }
    }
}
